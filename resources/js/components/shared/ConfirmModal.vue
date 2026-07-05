<template>
  <AppModal :modal-id="modalId" :title="displayTitle" size="sm">
    <p class="mb-0">{{ displayMessage }}</p>
    <template #footer>
      <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
      <button type="button" class="btn btn-danger btn-sm" :disabled="loading" @click="$emit('confirm')">
        <i v-if="loading" class="fas fa-spinner fa-spin me-1" />
        Confirm
      </button>
    </template>
  </AppModal>
</template>

<script>
import AppModal from './AppModal.vue';
import jqueryModal from '../../mixins/jqueryModal';

export default {
  name: 'ConfirmModal',
  components: { AppModal },
  mixins: [jqueryModal],
  props: {
    modalId: { type: String, default: 'confirm-modal' },
    title: { type: String, default: 'Confirm Action' },
    message: { type: String, default: 'Are you sure?' },
    loading: { type: Boolean, default: false },
  },
  emits: ['confirm'],
  data() {
    return {
      displayTitle: this.title,
      displayMessage: this.message,
    };
  },
  methods: {
    open(title, message) {
      this.displayTitle = title || this.title;
      this.displayMessage = message || this.message;
      this.showModal(this.modalId);
    },
  },
};
</script>
