<template>
  <AppModal :modal-id="modalId" :title="currentMode === 'import' ? 'Import Data' : 'Export Data'" size="md">
    <div v-if="currentMode === 'import'">
      <p class="text-muted">Upload a JSON file exported from this resource.</p>
      <input ref="fileInput" type="file" class="form-control" accept=".json,.txt" @change="onFileChange">
      <div v-if="error" class="alert alert-danger mt-3 mb-0">{{ error }}</div>
    </div>
    <div v-else>
      <p class="text-muted">Export current filtered records as JSON.</p>
      <div v-if="error" class="alert alert-danger mb-0">{{ error }}</div>
    </div>
    <template #footer>
      <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
      <button
        type="button"
        class="btn btn-primary btn-sm"
        :disabled="loading || (currentMode === 'import' && !file)"
        @click="submit"
      >
        <i v-if="loading" class="fas fa-spinner fa-spin me-1" />
        {{ currentMode === 'import' ? 'Import' : 'Download' }}
      </button>
    </template>
  </AppModal>
</template>

<script>
import AppModal from './AppModal.vue';
import jqueryModal from '../../mixins/jqueryModal';

export default {
  name: 'ImportExportModal',
  components: { AppModal },
  mixins: [jqueryModal],
  props: {
    modalId: { type: String, default: 'import-export-modal' },
    mode: { type: String, default: 'import' },
    endpoint: { type: String, required: true },
    queryParams: { type: Object, default: () => ({}) },
  },
  emits: ['imported'],
  data() {
    return {
      file: null,
      loading: false,
      error: null,
      currentMode: this.mode,
    };
  },
  methods: {
    open(mode) {
      this.currentMode = mode;
      this.error = null;
      this.file = null;
      if (this.$refs.fileInput) this.$refs.fileInput.value = '';
      this.showModal(this.modalId);
    },
    onFileChange(e) {
      this.file = e.target.files[0] || null;
    },
    async submit() {
      this.loading = true;
      this.error = null;
      try {
        if (this.currentMode === 'import') {
          const formData = new FormData();
          formData.append('file', this.file);
          Object.entries(this.queryParams).forEach(([key, value]) => {
            if (value !== undefined && value !== null && value !== '') {
              formData.append(key, value);
            }
          });
          await window.axios.post(`${this.endpoint}/import`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
          });
          this.hideModal(this.modalId);
          this.$emit('imported');
        } else {
          const params = new URLSearchParams(this.queryParams);
          const response = await window.axios.get(`${this.endpoint}/export?${params.toString()}`, {
            responseType: 'blob',
          });
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement('a');
          link.href = url;
          link.download = `${this.endpoint.split('/').pop()}-export.json`;
          link.click();
          window.URL.revokeObjectURL(url);
          this.hideModal(this.modalId);
        }
      } catch (err) {
        this.error = err.response?.data?.message || 'Operation failed.';
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>
