<template>
  <div ref="root" class="admin-multiselect-dropdown">
    <button
      type="button"
      class="admin-multiselect-trigger form-control text-start"
      :class="{ 'is-open': open }"
      :aria-expanded="open"
      @click="toggleOpen"
    >
      <span class="admin-multiselect-summary">{{ summaryText }}</span>
      <i class="fas fa-chevron-down admin-multiselect-chevron" />
    </button>
    <div v-if="open" class="admin-multiselect-menu">
      <label
        v-for="opt in options"
        :key="String(opt.value)"
        class="admin-multiselect-option"
      >
        <input
          type="checkbox"
          class="form-check-input"
          :checked="isSelected(opt.value)"
          @change="toggleOption(opt.value, $event.target.checked)"
        >
        <span>{{ opt.label }}</span>
      </label>
      <p v-if="!options.length" class="admin-multiselect-empty">No options available</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminMultiselectDropdown',
  props: {
    modelValue: { type: Array, default: () => [] },
    options: { type: Array, default: () => [] },
    placeholder: { type: String, default: 'Select options...' },
  },
  emits: ['update:modelValue'],
  data() {
    return { open: false };
  },
  computed: {
    summaryText() {
      if (!this.modelValue?.length) {
        return this.placeholder;
      }

      const labels = this.modelValue.map((val) => {
        const opt = this.options.find((item) => String(item.value) === String(val));
        return opt?.label || val;
      });

      return labels.join(', ');
    },
  },
  mounted() {
    document.addEventListener('click', this.onDocumentClick);
  },
  beforeUnmount() {
    document.removeEventListener('click', this.onDocumentClick);
  },
  methods: {
    toggleOpen() {
      this.open = !this.open;
    },
    onDocumentClick(event) {
      if (!this.$refs.root?.contains(event.target)) {
        this.open = false;
      }
    },
    isSelected(value) {
      return (this.modelValue || []).some((item) => String(item) === String(value));
    },
    toggleOption(value, checked) {
      const current = [...(this.modelValue || [])];
      const index = current.findIndex((item) => String(item) === String(value));

      if (checked && index === -1) {
        current.push(value);
      } else if (!checked && index !== -1) {
        current.splice(index, 1);
      }

      this.$emit('update:modelValue', current);
    },
  },
};
</script>
