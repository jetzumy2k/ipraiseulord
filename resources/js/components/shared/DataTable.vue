<template>
  <div class="card">
    <div class="card-header d-flex flex-wrap align-items-center gap-2">
      <div class="input-group input-group-sm" style="max-width: 280px;">
        <span class="input-group-text"><i class="fas fa-search" /></span>
        <input
          v-model="localSearch"
          type="search"
          class="form-control"
          placeholder="Search..."
          @keyup.enter="emitSearch"
        >
      </div>
      <button class="btn btn-sm btn-primary" type="button" @click="emitSearch">
        Search
      </button>
      <div v-for="filter in filters" :key="filter.key" class="ms-1">
        <select
          v-model="localFilters[filter.key]"
          class="form-select form-select-sm"
          @change="emitFilters"
        >
          <option value="">{{ filter.label || filter.key }}</option>
          <option v-for="opt in filter.options" :key="opt.value" :value="opt.value">
            {{ opt.label }}
          </option>
        </select>
      </div>
      <div v-if="showTrashedToggle" class="form-check ms-auto">
        <input
          id="show-trashed"
          v-model="localOnlyTrashed"
          class="form-check-input"
          type="checkbox"
          @change="emitTrashed"
        >
        <label class="form-check-label" for="show-trashed">Show deleted</label>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-striped table-hover mb-0">
        <thead>
          <tr>
            <th
              v-for="col in columns"
              :key="col.key"
              :class="{ 'sortable': col.sortable !== false }"
              role="button"
              @click="col.sortable !== false && toggleSort(col.key)"
            >
              {{ col.label }}
              <i
                v-if="sort === col.key"
                class="fas ms-1"
                :class="direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down'"
              />
            </th>
            <th v-if="showActions" class="text-end" style="width: 140px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td :colspan="columns.length + (showActions ? 1 : 0)" class="text-center py-4">
              <i class="fas fa-spinner fa-spin me-2" />Loading...
            </td>
          </tr>
          <tr v-else-if="!rows.length">
            <td :colspan="columns.length + (showActions ? 1 : 0)" class="text-center text-muted py-4">
              No records found.
            </td>
          </tr>
          <tr v-for="row in rows" :key="row.id">
            <td v-for="col in columns" :key="col.key">
              <slot :name="`cell-${col.key}`" :row="row" :value="formatCell(row, col)">
                {{ formatCell(row, col) }}
              </slot>
            </td>
            <td v-if="showActions" class="text-end">
              <slot name="actions" :row="row" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="pagination" class="card-footer d-flex flex-wrap align-items-center justify-content-between gap-2">
      <small class="text-muted">
        Showing {{ pagination.from || 0 }}–{{ pagination.to || 0 }} of {{ pagination.total || 0 }}
      </small>
      <nav>
        <ul class="pagination pagination-sm mb-0">
          <li class="page-item" :class="{ disabled: pagination.current_page <= 1 }">
            <button class="page-link" type="button" @click="changePage(pagination.current_page - 1)">
              Prev
            </button>
          </li>
          <li
            v-for="page in visiblePages"
            :key="page"
            class="page-item"
            :class="{ active: page === pagination.current_page }"
          >
            <button class="page-link" type="button" @click="changePage(page)">{{ page }}</button>
          </li>
          <li class="page-item" :class="{ disabled: pagination.current_page >= pagination.last_page }">
            <button class="page-link" type="button" @click="changePage(pagination.current_page + 1)">
              Next
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DataTable',
  props: {
    columns: { type: Array, required: true },
    rows: { type: Array, default: () => [] },
    loading: { type: Boolean, default: false },
    pagination: { type: Object, default: null },
    filters: { type: Array, default: () => [] },
    showActions: { type: Boolean, default: true },
    showTrashedToggle: { type: Boolean, default: false },
    sort: { type: String, default: 'id' },
    direction: { type: String, default: 'desc' },
    search: { type: String, default: '' },
    filterValues: { type: Object, default: () => ({}) },
    onlyTrashed: { type: Boolean, default: false },
  },
  emits: ['search', 'sort', 'page', 'filter', 'trashed'],
  data() {
    return {
      localSearch: this.search,
      localFilters: { ...this.filterValues },
      localOnlyTrashed: this.onlyTrashed,
    };
  },
  computed: {
    visiblePages() {
      if (!this.pagination) return [];
      const current = this.pagination.current_page;
      const last = this.pagination.last_page;
      const pages = [];
      for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i += 1) {
        pages.push(i);
      }
      return pages;
    },
  },
  watch: {
    search(val) {
      this.localSearch = val;
    },
    filterValues: {
      deep: true,
      handler(val) {
        this.localFilters = { ...val };
      },
    },
    onlyTrashed(val) {
      this.localOnlyTrashed = val;
    },
  },
  methods: {
    formatCell(row, col) {
      const value = col.accessor ? col.accessor(row) : row[col.key];
      if (col.format === 'boolean') return value ? 'Yes' : 'No';
      if (col.format === 'date' && value) return new Date(value).toLocaleDateString();
      if (col.format === 'datetime' && value) return new Date(value).toLocaleString();
      if (col.format === 'json' && value) return JSON.stringify(value);
      if (value === null || value === undefined || value === '') return '—';
      return value;
    },
    emitSearch() {
      this.$emit('search', this.localSearch);
    },
    emitFilters() {
      this.$emit('filter', { ...this.localFilters });
    },
    emitTrashed() {
      this.$emit('trashed', this.localOnlyTrashed);
    },
    toggleSort(key) {
      const direction = this.sort === key && this.direction === 'asc' ? 'desc' : 'asc';
      this.$emit('sort', { sort: key, direction });
    },
    changePage(page) {
      if (!this.pagination) return;
      if (page < 1 || page > this.pagination.last_page) return;
      this.$emit('page', page);
    },
  },
};
</script>
