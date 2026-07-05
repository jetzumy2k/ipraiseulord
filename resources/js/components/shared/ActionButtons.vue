<template>
  <div class="btn-group btn-group-sm">
    <button
      v-if="!row.deleted_at"
      class="btn btn-outline-primary"
      type="button"
      title="Edit"
      @click="$emit('edit', row)"
    >
      <i class="fas fa-edit" />
    </button>
    <button
      v-if="!row.deleted_at && canDelete"
      class="btn btn-outline-danger"
      type="button"
      title="Delete"
      @click="$emit('delete', row)"
    >
      <i class="fas fa-trash" />
    </button>
    <button
      v-if="row.deleted_at && softDeletes"
      class="btn btn-outline-success"
      type="button"
      title="Restore"
      @click="$emit('restore', row)"
    >
      <i class="fas fa-undo" />
    </button>
    <button
      v-if="row.deleted_at && softDeletes"
      class="btn btn-outline-danger"
      type="button"
      title="Force Delete"
      @click="$emit('force-delete', row)"
    >
      <i class="fas fa-times" />
    </button>
  </div>
</template>

<script>
export default {
  name: 'ActionButtons',
  props: {
    row: { type: Object, required: true },
    softDeletes: { type: Boolean, default: true },
    canDelete: { type: Boolean, default: true },
  },
  emits: ['edit', 'delete', 'restore', 'force-delete'],
};
</script>
