<template>
  <div class="install-wizard min-vh-100 d-flex flex-column">
    <header class="install-header text-center py-4">
      <h1 class="install-logo mb-1">Praise U Lord</h1>
      <p class="install-tagline mb-0">Setup Wizard</p>
    </header>

    <main class="flex-grow-1 d-flex align-items-start justify-content-center px-3 pb-5">
      <div class="install-card card shadow-sm w-100">
        <div class="card-body p-4 p-md-5">
          <nav class="install-steps mb-4" aria-label="Setup progress">
            <ol class="list-unstyled d-flex flex-wrap justify-content-center gap-2 mb-0">
              <li
                v-for="(label, index) in stepLabels"
                :key="label"
                class="install-step"
                :class="{
                  active: step === index + 1,
                  done: step > index + 1,
                }"
              >
                <span class="step-num">{{ index + 1 }}</span>
                <span class="step-label d-none d-md-inline">{{ label }}</span>
              </li>
            </ol>
          </nav>

          <div v-if="error" class="alert alert-danger">{{ error }}</div>

          <!-- Step 1: Welcome -->
          <section v-if="step === 1">
            <h2 class="h4 mb-3">Welcome</h2>
            <p>
              Thank you for choosing <strong>Praise U Lord</strong> — an online Bible and faith
              services platform for Roman Catholic communities.
            </p>
            <p class="text-muted mb-4">
              This wizard will guide you through checking server requirements, connecting your
              MySQL database, and creating your administrator account. The process takes a few
              minutes; Bible content import may take longer on first run.
            </p>
            <div class="d-flex justify-content-end">
              <button type="button" class="btn btn-primary" @click="nextStep">
                Let's go!
              </button>
            </div>
          </section>

          <!-- Step 2: Requirements -->
          <section v-if="step === 2">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h2 class="h4 mb-0">System requirements</h2>
              <button
                type="button"
                class="btn btn-sm btn-outline-secondary"
                :disabled="loadingRequirements"
                @click="loadRequirements"
              >
                <i v-if="loadingRequirements" class="fas fa-spinner fa-spin me-1" />
                Re-check
              </button>
            </div>

            <div v-if="loadingRequirements" class="text-center py-4 text-muted">
              <i class="fas fa-spinner fa-spin fa-2x mb-2" />
              <p class="mb-0">Checking your server…</p>
            </div>

            <template v-else-if="requirements">
              <table class="table table-sm requirements-table">
                <tbody>
                  <tr>
                    <td>PHP {{ requirements.php.required }}+</td>
                    <td>{{ requirements.php.current }}</td>
                    <td class="text-end">
                      <StatusBadge :passed="requirements.php.passed" />
                    </td>
                  </tr>
                  <tr v-for="(passed, ext) in requirements.extensions" :key="ext">
                    <td colspan="2">PHP extension: <code>{{ ext }}</code></td>
                    <td class="text-end"><StatusBadge :passed="passed" /></td>
                  </tr>
                  <tr v-for="item in requirements.writable" :key="item.label">
                    <td colspan="2">Writable: <code>{{ item.label }}</code></td>
                    <td class="text-end"><StatusBadge :passed="item.passed" /></td>
                  </tr>
                  <tr>
                    <td colspan="2">Compiled assets (<code>public/build</code>)</td>
                    <td class="text-end">
                      <StatusBadge :passed="requirements.assets.passed" />
                    </td>
                  </tr>
                </tbody>
              </table>

              <p v-if="!requirements.assets.passed" class="alert alert-warning small">
                Frontend assets are not built yet. On your computer run
                <code>npm install && npm run build</code>, then upload the
                <code>public/build</code> folder — or continue if you are in local development
                with Vite running.
              </p>

              <p v-if="!requirements.all_passed" class="text-danger small mb-0">
                Fix the failed items above before continuing. See
                <code>INSTALL.md</code> in the project root for help.
              </p>
            </template>

            <div class="d-flex justify-content-between mt-4">
              <button type="button" class="btn btn-outline-secondary" @click="prevStep">Back</button>
              <button
                type="button"
                class="btn btn-primary"
                :disabled="!requirements?.all_passed"
                @click="nextStep"
              >
                Continue
              </button>
            </div>
          </section>

          <!-- Step 3: Database -->
          <section v-if="step === 3">
            <h2 class="h4 mb-3">Database connection</h2>
            <p class="text-muted">
              Enter the MySQL credentials for the database you created. The wizard will create
              all tables automatically.
            </p>

            <form @submit.prevent="testDatabase">
              <div class="row g-3">
                <div class="col-md-8">
                  <label class="form-label" for="db-host">Database host</label>
                  <input id="db-host" v-model="form.database.host" type="text" class="form-control" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label" for="db-port">Port</label>
                  <input id="db-port" v-model.number="form.database.port" type="number" class="form-control" required>
                </div>
                <div class="col-12">
                  <label class="form-label" for="db-name">Database name</label>
                  <input id="db-name" v-model="form.database.database" type="text" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="db-user">Username</label>
                  <input id="db-user" v-model="form.database.username" type="text" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="db-pass">Password</label>
                  <input id="db-pass" v-model="form.database.password" type="password" class="form-control" autocomplete="new-password">
                </div>
              </div>

              <div v-if="dbTestMessage" class="alert mt-3 mb-0" :class="dbTestOk ? 'alert-success' : 'alert-danger'">
                {{ dbTestMessage }}
              </div>

              <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary" @click="prevStep">Back</button>
                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-outline-primary" :disabled="testingDb">
                    <i v-if="testingDb" class="fas fa-spinner fa-spin me-1" />
                    Test connection
                  </button>
                  <button type="button" class="btn btn-primary" :disabled="!dbTestOk" @click="nextStep">
                    Continue
                  </button>
                </div>
              </div>
            </form>
          </section>

          <!-- Step 4: Site & Admin -->
          <section v-if="step === 4">
            <h2 class="h4 mb-3">Site &amp; administrator</h2>

            <form @submit.prevent="nextStep">
              <h3 class="h6 text-muted text-uppercase mt-2 mb-3">Site settings</h3>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label" for="site-name">Site name</label>
                  <input id="site-name" v-model="form.site.name" type="text" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="site-url">Site URL</label>
                  <input id="site-url" v-model="form.site.url" type="url" class="form-control" required>
                  <div class="form-text">Use your full public URL, e.g. https://example.com</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="site-env">Environment</label>
                  <select id="site-env" v-model="form.site.environment" class="form-select">
                    <option value="production">Production</option>
                    <option value="local">Local / Development</option>
                    <option value="staging">Staging</option>
                  </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                  <div class="form-check">
                    <input id="site-debug" v-model="form.site.debug" class="form-check-input" type="checkbox">
                    <label class="form-check-label" for="site-debug">Enable debug mode</label>
                  </div>
                </div>
              </div>

              <h3 class="h6 text-muted text-uppercase mb-3">Administrator account</h3>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label" for="admin-name">Your name</label>
                  <input id="admin-name" v-model="form.admin.name" type="text" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="admin-email">Email</label>
                  <input id="admin-email" v-model="form.admin.email" type="email" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="admin-pass">Password</label>
                  <input id="admin-pass" v-model="form.admin.password" type="password" class="form-control" minlength="8" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="admin-pass2">Confirm password</label>
                  <input id="admin-pass2" v-model="form.admin.password_confirmation" type="password" class="form-control" minlength="8" required>
                </div>
              </div>

              <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary" @click="prevStep">Back</button>
                <button type="submit" class="btn btn-primary">Run installation</button>
              </div>
            </form>
          </section>

          <!-- Step 5: Installing -->
          <section v-if="step === 5">
            <div class="text-center py-4">
              <i class="fas fa-cog fa-spin fa-3x text-primary mb-3" />
              <h2 class="h4">Installing Praise U Lord…</h2>
              <p class="text-muted mb-0">
                Creating database tables and importing Bible content. This may take several
                minutes — please do not close this window.
              </p>
            </div>
          </section>

          <!-- Step 6: Complete -->
          <section v-if="step === 6">
            <div class="text-center py-3">
              <i class="fas fa-check-circle fa-3x text-success mb-3" />
              <h2 class="h4">All done!</h2>
              <p class="text-muted">
                Praise U Lord has been installed. Sign in to the admin panel with the email and
                password you created.
              </p>
              <div class="d-flex flex-wrap justify-content-center gap-2 mt-4">
                <router-link to="/" class="btn btn-primary">Visit your site</router-link>
                <router-link to="/admin/login" class="btn btn-outline-primary">Log in to admin</router-link>
              </div>
              <p class="small text-muted mt-4 mb-0">
                For security, keep <code>APP_DEBUG=false</code> in production and set up the
                cron job described in <code>INSTALL.md</code>.
              </p>
            </div>
          </section>
        </div>
      </div>
    </main>

    <footer class="install-footer text-center py-3 small text-muted">
      Praise U Lord Setup Wizard
    </footer>
  </div>
</template>

<script>
import axios from 'axios';

const StatusBadge = {
  name: 'StatusBadge',
  props: { passed: { type: Boolean, required: true } },
  template: `
    <span class="badge" :class="passed ? 'bg-success' : 'bg-danger'">
      {{ passed ? 'OK' : 'Failed' }}
    </span>
  `,
};

export default {
  name: 'InstallWizardPage',
  components: { StatusBadge },
  data() {
    return {
      step: 1,
      stepLabels: ['Welcome', 'Requirements', 'Database', 'Site & Admin', 'Install', 'Done'],
      error: null,
      requirements: null,
      loadingRequirements: false,
      testingDb: false,
      dbTestOk: false,
      dbTestMessage: '',
      form: {
        database: {
          host: '127.0.0.1',
          port: 3306,
          database: 'praise_u_lord',
          username: 'root',
          password: '',
        },
        site: {
          name: 'Praise U Lord',
          url: window.location.origin,
          environment: 'production',
          debug: false,
        },
        admin: {
          name: '',
          email: '',
          password: '',
          password_confirmation: '',
        },
      },
    };
  },
  watch: {
    step(value) {
      if (value === 2 && !this.requirements) {
        this.loadRequirements();
      }
      if (value === 5) {
        this.runInstall();
      }
    },
  },
  mounted() {
    this.detectLocalEnvironment();
  },
  methods: {
    detectLocalEnvironment() {
      const host = window.location.hostname;
      if (host === 'localhost' || host === '127.0.0.1') {
        this.form.site.environment = 'local';
        this.form.site.debug = true;
      }
    },
    async loadRequirements() {
      this.loadingRequirements = true;
      this.error = null;
      try {
        const { data } = await axios.get('/install/requirements');
        this.requirements = data;
      } catch {
        this.error = 'Could not check server requirements.';
      } finally {
        this.loadingRequirements = false;
      }
    },
    async testDatabase() {
      this.testingDb = true;
      this.dbTestMessage = '';
      this.dbTestOk = false;
      this.error = null;
      try {
        const { data } = await axios.post('/install/test-database', this.form.database);
        this.dbTestOk = data.success;
        this.dbTestMessage = data.message;
      } catch (err) {
        this.dbTestMessage = err.response?.data?.message || 'Database connection failed.';
      } finally {
        this.testingDb = false;
      }
    },
    async runInstall() {
      this.error = null;
      try {
        const { data } = await axios.post('/install/run', this.form);
        if (data.success) {
          this.step = 6;
        } else {
          this.error = data.message;
          this.step = 4;
        }
      } catch (err) {
        this.error = err.response?.data?.message || 'Installation failed.';
        this.step = 4;
      }
    },
    nextStep() {
      if (this.step === 4) {
        if (this.form.admin.password !== this.form.admin.password_confirmation) {
          this.error = 'Passwords do not match.';
          return;
        }
      }
      this.error = null;
      this.step += 1;
    },
    prevStep() {
      this.error = null;
      this.step -= 1;
    },
  },
};
</script>

<style scoped>
.install-wizard {
  background: linear-gradient(180deg, #faf7f2 0%, #f0ebe3 100%);
  font-family: 'Lato', system-ui, sans-serif;
  color: #2c2419;
}

.install-header {
  background: #5c2e2e;
  color: #f5ebe8;
}

.install-logo {
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: 2rem;
  font-weight: 700;
  letter-spacing: 0.02em;
}

.install-tagline {
  opacity: 0.85;
  font-size: 0.95rem;
}

.install-card {
  max-width: 720px;
  border: none;
  border-radius: 0.5rem;
}

.install-steps .install-step {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.8rem;
  color: #6b5d4f;
}

.install-steps .step-num {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 1.75rem;
  height: 1.75rem;
  border-radius: 50%;
  background: #e8e0d5;
  font-weight: 700;
  font-size: 0.75rem;
}

.install-step.active .step-num {
  background: #5c2e2e;
  color: #fff;
}

.install-step.done .step-num {
  background: #198754;
  color: #fff;
}

.install-step.active {
  color: #5c2e2e;
  font-weight: 700;
}

.requirements-table td {
  vertical-align: middle;
}

.btn-primary {
  background: #5c2e2e;
  border-color: #5c2e2e;
}

.btn-primary:hover,
.btn-primary:focus {
  background: #7a4545;
  border-color: #7a4545;
}

.btn-outline-primary {
  color: #5c2e2e;
  border-color: #5c2e2e;
}

.btn-outline-primary:hover {
  background: #5c2e2e;
  border-color: #5c2e2e;
}
</style>
