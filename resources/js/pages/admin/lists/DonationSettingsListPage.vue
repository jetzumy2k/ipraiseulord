<template>
  <div>
    <div class="donation-admin-controls card border-0 shadow-sm mb-3">
      <div class="card-body d-flex flex-wrap align-items-center gap-3">
        <div class="donation-admin-controls__global">
          <span class="admin-form-label mb-0 me-2">Public donations</span>
          <div class="admin-form-switch-control">
            <span class="admin-form-switch-status">
              {{ donationsEnabled ? 'Visible on site' : 'Hidden on site' }}
            </span>
            <div class="form-check form-switch mb-0">
              <input
                id="donations-global-toggle"
                v-model="donationsEnabled"
                class="form-check-input"
                type="checkbox"
                role="switch"
                :disabled="statusLoading || globalSaving"
                @change="saveGlobalToggle"
              >
            </div>
          </div>
        </div>

        <div class="donation-admin-controls__bulk btn-group btn-group-sm">
          <button
            type="button"
            class="btn btn-outline-success"
            :disabled="bulkSaving"
            @click="setAllActive(true)"
          >
            <i class="fas fa-check-double me-1" />Enable All
          </button>
          <button
            type="button"
            class="btn btn-outline-secondary"
            :disabled="bulkSaving"
            @click="setAllActive(false)"
          >
            <i class="fas fa-ban me-1" />Disable All
          </button>
        </div>

        <div v-if="statusLoaded" class="donation-admin-controls__summary text-muted small ms-md-auto">
          {{ activeCount }} of {{ totalCount }} methods enabled
          <span v-if="!donationsEnabled" class="text-warning ms-1">(hidden from public pages)</span>
        </div>
      </div>
    </div>

    <AdminCrudPage
      ref="crudPage"
      :config="config"
      @row-toggled="loadStatus"
      @rows-loaded="loadStatus"
    />
  </div>
</template>

<script>
import AdminCrudPage from '../../../components/shared/AdminCrudPage.vue';
import { resourceConfigs } from '../config/resourceConfigs';

export default {
  name: 'DonationSettingsListPage',
  components: { AdminCrudPage },
  data() {
    return {
      donationsEnabled: true,
      activeCount: 0,
      totalCount: 0,
      statusLoaded: false,
      statusLoading: false,
      globalSaving: false,
      bulkSaving: false,
    };
  },
  computed: {
    config() {
      return resourceConfigs['donation-settings'];
    },
  },
  mounted() {
    this.loadStatus();
  },
  methods: {
    async loadStatus() {
      this.statusLoading = true;
      try {
        const { data } = await window.axios.get('/donation-settings/status');
        this.donationsEnabled = !!data.donations_enabled;
        this.activeCount = data.active_count ?? 0;
        this.totalCount = data.total_count ?? 0;
        this.statusLoaded = true;
      } finally {
        this.statusLoading = false;
      }
    },
    async saveGlobalToggle() {
      this.globalSaving = true;
      try {
        const { data } = await window.axios.post('/donation-settings/toggle-global', {
          enabled: this.donationsEnabled,
        });
        this.donationsEnabled = !!data.donations_enabled;
        this.activeCount = data.active_count ?? 0;
        this.totalCount = data.total_count ?? 0;
      } catch {
        this.donationsEnabled = !this.donationsEnabled;
      } finally {
        this.globalSaving = false;
      }
    },
    async setAllActive(isActive) {
      this.bulkSaving = true;
      try {
        const { data } = await window.axios.post('/donation-settings/bulk-active', { is_active: isActive });
        this.activeCount = data.active_count ?? 0;
        this.totalCount = data.total_count ?? 0;
        await this.$refs.crudPage.fetchRows();
      } finally {
        this.bulkSaving = false;
      }
    },
  },
};
</script>
