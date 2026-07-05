<template>
  <div class="d-flex flex-wrap gap-2 mb-3">
    <button v-if="canCreate" class="btn btn-primary btn-sm" type="button" @click="$emit('add')">
      <i class="fas fa-plus me-1" />Add New
    </button>
    <button v-if="canExport" class="btn btn-outline-secondary btn-sm" type="button" @click="$emit('export')">
      <i class="fas fa-download me-1" />Export
    </button>
    <button v-if="canImport" class="btn btn-outline-secondary btn-sm" type="button" @click="$emit('import')">
      <i class="fas fa-upload me-1" />Import
    </button>
    <slot />
  </div>
</template>

<script>
export default {
  name: 'CrudToolbar',
  props: {
    canCreate: { type: Boolean, default: true },
    canExport: { type: Boolean, default: true },
    canImport: { type: Boolean, default: true },
  },
  emits: ['add', 'export', 'import'],
};
</script>
