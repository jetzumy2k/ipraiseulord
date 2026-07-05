<template>
  <div class="seo-settings-page">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <div>
        <h1 class="h3 mb-1">SEO Settings</h1>
        <p class="text-muted mb-0">
          Customize search and social sharing metadata for your site and main pages.
        </p>
      </div>
      <button type="button" class="btn btn-primary" :disabled="saving" @click="save">
        <i v-if="saving" class="fas fa-spinner fa-spin me-1" />
        Save Changes
      </button>
    </div>

    <div v-if="loading" class="text-center py-5">
      <i class="fas fa-spinner fa-spin fa-2x" />
    </div>

    <template v-else>
      <div v-if="message" class="alert" :class="messageType === 'success' ? 'alert-success' : 'alert-danger'">
        {{ message }}
      </div>

      <div class="card mb-4">
        <div class="card-header">
          <h2 class="card-title h5 mb-0">Global SEO</h2>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Home Page Headline</label>
              <input v-model="form.home_headline" type="text" class="form-control">
              <small class="text-muted">
                Appears after the site name on the home page and when sharing the homepage.
                Preview: <strong>{{ siteName }} — {{ form.home_headline || '…' }}</strong>
              </small>
            </div>
            <div class="col-md-6">
              <label class="form-label">Twitter / X Handle</label>
              <input v-model="form.twitter_handle" type="text" class="form-control" placeholder="@yourhandle">
            </div>
            <div class="col-12">
              <label class="form-label">Default Meta Description</label>
              <textarea v-model="form.default_description" class="form-control" rows="3" maxlength="500" />
              <small class="text-muted">{{ (form.default_description || '').length }}/500 characters</small>
            </div>
            <div class="col-12">
              <label class="form-label">Default Keywords</label>
              <input v-model="form.keywords" type="text" class="form-control">
              <small class="text-muted">Comma-separated keywords used when a page has no specific keywords.</small>
            </div>
            <div class="col-md-8">
              <label class="form-label">Default Share Image (Open Graph)</label>
              <input v-model="form.og_image" type="text" class="form-control" placeholder="/images/og-default.png">
              <small class="text-muted">Recommended size 1200 × 630 px. Used on Facebook, Messenger, and Twitter previews.</small>
            </div>
            <div class="col-md-4">
              <label class="form-label">Upload Share Image</label>
              <input type="file" class="form-control" accept="image/*" @change="onImageSelected">
            </div>
            <div v-if="ogImagePreview" class="col-12">
              <img :src="ogImagePreview" alt="Share image preview" class="seo-settings-preview">
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h2 class="card-title h5 mb-0">Page SEO</h2>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped mb-0">
              <thead>
                <tr>
                  <th style="min-width: 10rem;">Page</th>
                  <th style="min-width: 12rem;">SEO Title</th>
                  <th>Meta Description</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="page in pages" :key="page.key">
                  <td>
                    <strong>{{ page.label }}</strong>
                    <div class="small text-muted">{{ page.path }}</div>
                    <span v-if="page.managed_in_static_pages" class="badge bg-light text-dark mt-1">
                      Also editable in Static Pages
                    </span>
                  </td>
                  <td>
                    <input
                      v-model="form.page_meta[page.key].title"
                      type="text"
                      class="form-control form-control-sm"
                      :placeholder="page.default_title"
                    >
                  </td>
                  <td>
                    <textarea
                      v-model="form.page_meta[page.key].description"
                      class="form-control form-control-sm"
                      rows="2"
                      maxlength="500"
                      :placeholder="page.default_description"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="alert alert-info mt-4 mb-0">
        <strong>Tip:</strong> After changing SEO settings, refresh social previews using the
        <a href="https://developers.facebook.com/tools/debug/" target="_blank" rel="noopener">Facebook Sharing Debugger</a>.
        Individual novena and prayer pages use their own titles automatically.
      </div>
    </template>
  </div>
</template>

<script>
export default {
  name: 'SeoSettingsPage',
  data() {
    return {
      loading: true,
      saving: false,
      message: null,
      messageType: 'success',
      siteName: 'Praise U Lord',
      pages: [],
      form: {
        home_headline: '',
        default_description: '',
        keywords: '',
        twitter_handle: '',
        og_image: '',
        og_image_file: null,
        page_meta: {},
      },
    };
  },
  computed: {
    ogImagePreview() {
      if (this.form.og_image_file) {
        return URL.createObjectURL(this.form.og_image_file);
      }

      const path = this.form.og_image;
      if (!path) {
        return null;
      }

      if (/^https?:\/\//i.test(path)) {
        return path;
      }

      return path.startsWith('/') ? path : `/${path}`;
    },
  },
  mounted() {
    this.load();
  },
  methods: {
    async load() {
      this.loading = true;
      try {
        const { data } = await window.axios.get('/seo-settings');
        this.siteName = data.site_name || this.siteName;
        this.pages = data.pages || [];
        this.form.home_headline = data.global.home_headline || '';
        this.form.default_description = data.global.default_description || '';
        this.form.keywords = data.global.keywords || '';
        this.form.twitter_handle = data.global.twitter_handle || '';
        this.form.og_image = data.global.og_image || '';

        const pageMeta = {};
        this.pages.forEach((page) => {
          pageMeta[page.key] = {
            title: page.title || '',
            description: page.description || '',
          };
        });
        this.form.page_meta = pageMeta;
      } catch {
        this.message = 'Failed to load SEO settings.';
        this.messageType = 'danger';
      } finally {
        this.loading = false;
      }
    },
    onImageSelected(event) {
      const [file] = event.target.files || [];
      this.form.og_image_file = file || null;
    },
    async save() {
      this.saving = true;
      this.message = null;

      try {
        const payload = new FormData();
        payload.append('home_headline', this.form.home_headline);
        payload.append('default_description', this.form.default_description);
        payload.append('keywords', this.form.keywords);
        payload.append('twitter_handle', this.form.twitter_handle);
        payload.append('og_image', this.form.og_image);

        Object.entries(this.form.page_meta).forEach(([key, values]) => {
          payload.append(`page_meta[${key}][title]`, values.title || '');
          payload.append(`page_meta[${key}][description]`, values.description || '');
        });

        if (this.form.og_image_file) {
          payload.append('og_image_file', this.form.og_image_file);
        }

        const { data } = await window.axios.post('/seo-settings', payload, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });

        this.message = data.message || 'SEO settings saved.';
        this.messageType = 'success';
        this.form.og_image_file = null;
        await this.load();
      } catch (err) {
        this.message = err.response?.data?.message || 'Failed to save SEO settings.';
        this.messageType = 'danger';
      } finally {
        this.saving = false;
      }
    },
  },
};
</script>

<style scoped>
.seo-settings-preview {
  max-width: 420px;
  width: 100%;
  border: 1px solid rgba(0, 0, 0, 0.1);
  border-radius: 4px;
}
</style>
