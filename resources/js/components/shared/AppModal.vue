<template>
  <div
    :id="modalId"
    class="modal fade admin-modal"
    tabindex="-1"
    :aria-labelledby="`${modalId}-label`"
    aria-hidden="true"
  >
    <div class="modal-dialog" :class="dialogClass">
      <div class="modal-content">
        <div class="modal-header">
          <h5 :id="`${modalId}-label`" class="modal-title">{{ title }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" />
        </div>
        <div class="modal-body">
          <slot />
        </div>
        <div v-if="$slots.footer" class="modal-footer">
          <slot name="footer" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AppModal',
  props: {
    modalId: { type: String, required: true },
    title: { type: String, default: '' },
    size: { type: String, default: 'md' },
    scrollable: { type: Boolean, default: true },
  },
  computed: {
    sizeClass() {
      const sizes = {
        sm: 'modal-sm',
        lg: 'modal-lg',
        xl: 'modal-xl',
      };
      return sizes[this.size] || '';
    },
    dialogClass() {
      return [
        this.sizeClass,
        this.scrollable ? 'modal-dialog-scrollable' : '',
      ].filter(Boolean);
    },
  },
};
</script>
