<template>
  <div>
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h1 class="h3 mb-0">{{ config.title }}</h1>
    </div>

    <CrudToolbar
      :can-create="config.canCreate !== false"
      :can-export="config.canExport !== false"
      :can-import="config.canImport !== false"
      @add="openCreate"
      @export="openExport"
      @import="openImport"
    />

    <DataTable
      :columns="config.columns"
      :rows="rows"
      :loading="loading"
      :pagination="pagination"
      :filters="resolvedFilters"
      :show-trashed-toggle="config.softDeletes !== false"
      :sort="sort"
      :direction="direction"
      :search="search"
      :filter-values="filters"
      :only-trashed="onlyTrashed"
      @search="onSearch"
      @sort="onSort"
      @page="onPage"
      @filter="onFilter"
      @trashed="onTrashed"
    >
      <template v-if="config.quickToggleField" #[quickToggleSlotName]="{ row }">
        <div class="form-check form-switch mb-0 admin-table-switch">
          <input
            :id="`${formModalId}-toggle-${row.id}`"
            class="form-check-input"
            type="checkbox"
            role="switch"
            :checked="!!row[config.quickToggleField]"
            :disabled="!!rowToggleLoading[row.id]"
            @change="toggleRowField(row)"
          >
        </div>
      </template>
      <template #actions="{ row }">
        <ActionButtons
          :row="row"
          :soft-deletes="config.softDeletes !== false"
          :can-delete="config.canDelete !== false"
          @edit="openEdit"
          @delete="confirmDelete"
          @restore="restoreRow"
          @force-delete="confirmForceDelete"
        />
      </template>
    </DataTable>

    <AppModal :modal-id="formModalId" :title="formTitle" :size="config.formSize || 'lg'">
      <form class="admin-crud-form" @submit.prevent="saveRecord">
        <div v-if="formError" class="alert alert-danger mb-3">{{ formError }}</div>
        <div class="row g-3">
          <div
            v-for="field in visibleFormFields"
            :key="field.key"
            :class="field.col || 'col-12'"
          >
            <label
              v-if="field.type !== 'boolean'"
              :for="`${formModalId}-${field.key}`"
              class="form-label admin-form-label"
            >
              {{ field.label }}
              <span v-if="field.required" class="text-danger">*</span>
            </label>

            <select
              v-if="field.type === 'select'"
              :id="`${formModalId}-${field.key}`"
              v-model="form[field.key]"
              class="form-select"
              :required="field.required"
              :disabled="isFieldDisabled(field)"
              @change="onFieldChange(field)"
            >
              <option :value="field.emptyValue ?? ''">{{ field.placeholder || 'Select...' }}</option>
              <option v-for="opt in fieldOptions(field)" :key="`${field.key}-${opt.value}`" :value="opt.value">
                {{ opt.label }}
              </option>
            </select>

            <div v-else-if="field.type === 'boolean'" class="admin-form-field admin-form-field--switch">
              <div class="admin-form-switch-row">
                <label :for="`${formModalId}-${field.key}`" class="admin-form-label admin-form-switch-label mb-0">
                  {{ field.label }}
                </label>
                <div class="admin-form-switch-control">
                  <span class="admin-form-switch-status">{{ form[field.key] ? 'Enabled' : 'Disabled' }}</span>
                  <div class="form-check form-switch mb-0">
                    <input
                      :id="`${formModalId}-${field.key}`"
                      v-model="form[field.key]"
                      class="form-check-input"
                      type="checkbox"
                      role="switch"
                      :aria-label="field.label"
                    >
                  </div>
                </div>
              </div>
            </div>

            <div v-else-if="field.type === 'image'" class="admin-form-image">
              <input
                :id="`${formModalId}-${field.key}`"
                class="form-control"
                type="file"
                accept="image/*"
                @change="onImageChange(field, $event)"
              >
              <div v-if="imagePreview(field)" class="admin-form-image-preview mt-2">
                <img :src="imagePreview(field)" :alt="`${field.label} preview`">
              </div>
            </div>

            <div v-else-if="field.type === 'banner-image'" class="admin-form-image">
              <input
                :id="`${formModalId}-${field.key}`"
                class="form-control"
                type="file"
                accept="image/*"
                @change="onBannerImageChange(field, $event)"
              >
              <div v-if="imagePreview(field)" class="admin-form-image-preview admin-form-image-preview--banner mt-2">
                <img :src="imagePreview(field)" :alt="`${field.label} preview`">
              </div>
            </div>

            <PageBuilderEditor
              v-else-if="field.type === 'page-builder'"
              v-model="form[field.key]"
            />

            <RichTextEditor
              v-else-if="isRichTextField(field)"
              v-model="form[field.key]"
              :editor-key="`${formModalId}-${field.key}-${editingId || 'new'}`"
              :placeholder="field.placeholder || `Enter ${field.label.toLowerCase()}...`"
              :compact="field.compact !== false"
            />

            <textarea
              v-else-if="field.type === 'textarea'"
              :id="`${formModalId}-${field.key}`"
              v-model="form[field.key]"
              class="form-control font-monospace"
              rows="5"
              :required="field.required"
              :placeholder="field.placeholder"
            />

            <AdminMultiselectDropdown
              v-else-if="field.type === 'multiselect'"
              v-model="form[field.key]"
              :options="multiselectOptions(field)"
              :placeholder="field.placeholder || `Select ${field.label.toLowerCase()}...`"
            />

            <input
              v-else-if="field.type === 'json'"
              :id="`${formModalId}-${field.key}`"
              v-model="jsonFields[field.key]"
              class="form-control font-monospace"
              type="text"
              :placeholder="field.placeholder || '[]'"
            >

            <input
              v-else
              :id="`${formModalId}-${field.key}`"
              v-model="form[field.key]"
              class="form-control"
              :type="field.type || 'text'"
              :required="field.required"
              :step="field.step"
              :placeholder="field.placeholder"
            >

            <div v-if="field.help" class="form-text">{{ field.help }}</div>
          </div>
        </div>
      </form>
      <template #footer>
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" :disabled="saving" @click="saveRecord">
          <i v-if="saving" class="fas fa-spinner fa-spin me-1" />
          Save Changes
        </button>
      </template>
    </AppModal>

    <ConfirmModal
      ref="confirmModal"
      :modal-id="confirmModalId"
      :loading="saving"
      :title="confirmTitle"
      :message="confirmMessage"
      @confirm="executeConfirm"
    />

    <ImportExportModal
      ref="importExportModal"
      :modal-id="importExportModalId"
      :mode="importExportMode"
      :endpoint="config.endpoint"
      :query-params="queryParams"
      @imported="fetchRows"
    />

    <BannerImageCropper
      ref="bannerCropper"
      :modal-id="bannerCropperModalId"
      @cropped="onBannerCropped"
    />
  </div>
</template>

<script>
import AppModal from './AppModal.vue';
import DataTable from './DataTable.vue';
import CrudToolbar from './CrudToolbar.vue';
import ActionButtons from './ActionButtons.vue';
import ConfirmModal from './ConfirmModal.vue';
import ImportExportModal from './ImportExportModal.vue';
import RichTextEditor from './RichTextEditor.vue';
import PageBuilderEditor from './PageBuilderEditor.vue';
import AdminMultiselectDropdown from './AdminMultiselectDropdown.vue';
import BannerImageCropper from './BannerImageCropper.vue';
import jqueryModal from '../../mixins/jqueryModal';

export default {
  name: 'AdminCrudPage',
  components: {
    AppModal,
    DataTable,
    CrudToolbar,
    ActionButtons,
    ConfirmModal,
    ImportExportModal,
    RichTextEditor,
    PageBuilderEditor,
    AdminMultiselectDropdown,
    BannerImageCropper,
  },
  mixins: [jqueryModal],
  props: {
    config: { type: Object, required: true },
  },
  emits: ['rows-loaded', 'row-toggled'],
  data() {
    return {
      rows: [],
      pagination: null,
      loading: false,
      saving: false,
      search: '',
      sort: 'id',
      direction: 'desc',
      page: 1,
      filters: {},
      onlyTrashed: false,
      form: {},
      jsonFields: {},
      formTitle: '',
      formError: null,
      editingId: null,
      confirmTitle: 'Confirm',
      confirmMessage: '',
      confirmAction: null,
      importExportMode: 'import',
      relationOptions: {},
      imageFiles: {},
      activeBannerFieldKey: null,
      bannerCropperModalId: 'banner-image-cropper-modal',
      objectFieldSources: {},
      rowToggleLoading: {},
    };
  },
  computed: {
    formModalId() {
      return `${this.config.key}-form-modal`;
    },
    confirmModalId() {
      return `${this.config.key}-confirm-modal`;
    },
    importExportModalId() {
      return `${this.config.key}-import-export-modal`;
    },
    queryParams() {
      const params = {
        search: this.search || undefined,
        sort: this.sort,
        direction: this.direction,
        page: this.page,
        ...this.filters,
      };
      if (this.onlyTrashed) params.only_trashed = 1;
      return params;
    },
    visibleFormFields() {
      return this.config.formFields.filter((field) => this.isFieldVisible(field));
    },
    hasFileUploadFields() {
      return this.config.formFields.some((field) => field.type === 'image' || field.type === 'banner-image');
    },
    quickToggleSlotName() {
      return this.config.quickToggleField
        ? `cell-${this.config.quickToggleField}`
        : null;
    },
    resolvedFilters() {
      return (this.config.filters || []).map((filter) => ({
        ...filter,
        options: filter.relation
          ? (this.relationOptions[`filter_${filter.key}`] || [])
          : (filter.options || []),
      }));
    },
  },
  mounted() {
    this.fetchRows();
    this.loadRelationOptions();
  },
  methods: {
    defaultForm() {
      const form = {};
      this.config.formFields.forEach((field) => {
        if (field.type === 'boolean') {
          form[field.key] = false;
        } else if (field.type === 'multiselect') {
          form[field.key] = field.default ?? [];
        } else {
          form[field.key] = field.default ?? '';
        }
      });
      return form;
    },
    isRichTextField(field) {
      return field.type === 'richtext'
        || (field.type === 'textarea' && field.plainText !== true);
    },
    isFieldVisible(field) {
      if (!field.dependsOn) {
        return true;
      }

      return this.form[field.dependsOn] !== '' && this.form[field.dependsOn] !== null && this.form[field.dependsOn] !== undefined;
    },
    isFieldDisabled(field) {
      if (field.disableOnEdit && this.editingId) {
        return true;
      }

      if (!field.dependsOn) {
        return false;
      }

      return !this.form[field.dependsOn];
    },
    relationStorageKey(field, prefix = '') {
      return `${prefix}${field.key}`;
    },
    async loadRelationOptions() {
      const formRelationFields = this.config.formFields.filter((field) => field.relation);
      const filterRelationFields = (this.config.filters || []).filter((filter) => filter.relation);

      await Promise.all([
        ...formRelationFields.map((field) => this.loadRelationOptionsForField(field)),
        ...filterRelationFields.map((filter) => this.loadRelationOptionsForField({
          key: filter.key,
          relation: filter.relation,
        }, 'filter_')),
      ]);
    },
    async loadRelationOptionsForField(field, prefix = '') {
      if ((field.dependsOn && !this.form[field.dependsOn])
        || (field.relation?.dependsOn && !this.form[field.relation.dependsOn])) {
        this.relationOptions[this.relationStorageKey(field, prefix)] = [];
        return;
      }

      const params = {
        per_page: field.relation.perPage || 200,
        sort: field.relation.sort || 'id',
        direction: field.relation.direction || 'desc',
        ...field.relation.params,
      };

      if (field.relation.paramsFrom && this.form[field.relation.paramsFrom]) {
        params[field.relation.paramKey || field.relation.paramsFrom.replace(/^_/, '')] = this.form[field.relation.paramsFrom];
      }

      const { data } = await window.axios.get(field.relation.endpoint, { params });
      const items = data.data || data;

      this.relationOptions[this.relationStorageKey(field, prefix)] = items.map((item) => ({
        value: this.normalizeOptionValue(item[field.relation.valueKey || 'id']),
        label: typeof field.relation.label === 'function'
          ? field.relation.label(item)
          : item[field.relation.label || 'name'],
      }));
    },
    normalizeOptionValue(value) {
      if (value === null || value === undefined || value === '') {
        return '';
      }

      const numeric = Number(value);
      return Number.isNaN(numeric) ? value : numeric;
    },
    ensureRelationOption(fieldKey, item, labelBuilder) {
      const value = this.normalizeOptionValue(item.id || item.value);
      const options = this.relationOptions[fieldKey] || [];
      const exists = options.some((option) => String(option.value) === String(value));

      if (!exists) {
        options.unshift({
          value,
          label: typeof labelBuilder === 'function' ? labelBuilder(item) : labelBuilder,
        });
        this.relationOptions[fieldKey] = options;
      }
    },
    fieldOptions(field) {
      if (field.options) return field.options;
      if (field.relation) return this.relationOptions[this.relationStorageKey(field)] || [];
      return [];
    },
    multiselectOptions(field) {
      if (field.optionsFromData && field.objectKey) {
        const source = this.objectFieldSources[field.key] || [];

        return source.map((item) => ({
          value: item[field.objectKey],
          label: item[field.objectKey],
        }));
      }

      return this.fieldOptions(field);
    },
    async onFieldChange(field) {
      const dependentFields = this.config.formFields.filter((item) => item.dependsOn === field.key);

      for (const dependentField of dependentFields) {
        this.form[dependentField.key] = '';
        await this.loadRelationOptionsForField(dependentField);
      }
    },
    async fetchRows() {
      this.loading = true;
      try {
        const { data } = await window.axios.get(this.config.endpoint, { params: this.queryParams });
        this.rows = data.data || [];
        this.pagination = {
          current_page: data.current_page,
          last_page: data.last_page,
          from: data.from,
          to: data.to,
          total: data.total,
        };
        this.$emit('rows-loaded', this.rows);
      } finally {
        this.loading = false;
      }
    },
    async toggleRowField(row) {
      if (!this.config.quickToggleField) {
        return;
      }

      const fieldKey = this.config.quickToggleField;
      const nextValue = !row[fieldKey];

      this.rowToggleLoading = { ...this.rowToggleLoading, [row.id]: true };

      try {
        await window.axios.put(`${this.config.endpoint}/${row.id}`, {
          [fieldKey]: nextValue,
        });
        await this.fetchRows();
        this.$emit('row-toggled', { row, field: fieldKey, value: nextValue });
      } catch (err) {
        this.formError = err.response?.data?.message || 'Update failed.';
      } finally {
        const loading = { ...this.rowToggleLoading };
        delete loading[row.id];
        this.rowToggleLoading = loading;
      }
    },
    onSearch(value) {
      this.search = value;
      this.page = 1;
      this.fetchRows();
    },
    onSort({ sort, direction }) {
      this.sort = sort;
      this.direction = direction;
      this.fetchRows();
    },
    onPage(page) {
      this.page = page;
      this.fetchRows();
    },
    onFilter(values) {
      this.filters = values;
      this.page = 1;
      this.fetchRows();
    },
    onTrashed(value) {
      this.onlyTrashed = value;
      this.page = 1;
      this.fetchRows();
    },
    openCreate() {
      this.editingId = null;
      this.formTitle = `Add ${this.config.titleSingular || this.config.title}`;
      this.form = this.defaultForm();
      this.imageFiles = {};
      this.objectFieldSources = {};
      this.syncJsonFields();
      this.formError = null;
      this.showModal(this.formModalId);
    },
    async openEdit(row) {
      this.editingId = row.id;
      this.formTitle = `Edit ${this.config.titleSingular || this.config.title}`;
      this.form = this.defaultForm();

      this.config.formFields.forEach((field) => {
        if (field.type === 'multiselect') {
          const value = row[field.key];

          if (field.objectKey && Array.isArray(value)) {
            this.objectFieldSources[field.key] = value;
            this.form[field.key] = value
              .map((item) => (typeof item === 'object' ? item[field.objectKey] : item))
              .filter((item) => item !== null && item !== undefined && item !== '');
          } else {
            this.form[field.key] = Array.isArray(value) ? [...value] : [];
          }

          return;
        }

        if (row[field.key] !== undefined && row[field.key] !== null) {
          let value = row[field.key];

          if (field.type === 'date' && typeof value === 'string') {
            value = value.slice(0, 10);
          } else if (field.type === 'datetime-local' && typeof value === 'string') {
            value = value.replace(' ', 'T').slice(0, 16);
          }

          this.form[field.key] = field.type === 'select'
            ? this.normalizeOptionValue(value)
            : value;
        }
      });

      if (row.chapter?.bible_book_id || row.chapter?.book?.id) {
        this.form._bible_book_id = this.normalizeOptionValue(row.chapter.bible_book_id || row.chapter.book.id);
        const chapterField = this.config.formFields.find((field) => field.key === 'bible_chapter_id');
        if (chapterField) {
          await this.loadRelationOptionsForField(chapterField);
        }
      }

      if (row.bible_chapter_id) {
        this.form.bible_chapter_id = this.normalizeOptionValue(row.bible_chapter_id);
        this.ensureRelationOption(
          'bible_chapter_id',
          row.chapter || { id: row.bible_chapter_id },
          (item) => `${item.book?.name || 'Book'} ${item.chapter_number || ''}`.trim(),
        );
      }

      this.syncJsonFields();
      this.imageFiles = {};
      this.objectFieldSources = { ...this.objectFieldSources };
      this.formError = null;
      this.showModal(this.formModalId);
    },
    syncJsonFields() {
      this.config.formFields.filter((field) => field.type === 'json').forEach((field) => {
        const val = this.form[field.key];
        this.jsonFields[field.key] = val ? JSON.stringify(val) : '[]';
      });
    },
    preparePayload() {
      const payload = { ...this.form };

      this.config.formFields.forEach((field) => {
        if (field.virtual) {
          delete payload[field.key];
        }

        if (field.type === 'json') {
          try {
            payload[field.key] = JSON.parse(this.jsonFields[field.key] || '[]');
          } catch {
            throw new Error(`Invalid JSON for ${field.label}`);
          }
        }

        if (field.type === 'multiselect' && field.objectKey && this.objectFieldSources[field.key]) {
          const selected = payload[field.key] || [];
          payload[field.key] = this.objectFieldSources[field.key]
            .filter((item) => selected.includes(item[field.objectKey]));
        }
      });

      return payload;
    },
    onImageChange(field, event) {
      const file = event.target.files?.[0];

      if (file) {
        this.imageFiles[field.key] = file;
      } else {
        delete this.imageFiles[field.key];
      }
    },
    onBannerImageChange(field, event) {
      const file = event.target.files?.[0];
      event.target.value = '';

      if (!file) {
        delete this.imageFiles[field.key];
        return;
      }

      this.activeBannerFieldKey = field.key;
      this.$refs.bannerCropper?.open(file);
    },
    onBannerCropped(file) {
      if (!this.activeBannerFieldKey) {
        return;
      }

      this.imageFiles[this.activeBannerFieldKey] = file;
      this.activeBannerFieldKey = null;
    },
    imagePreview(field) {
      if (this.imageFiles[field.key]) {
        return URL.createObjectURL(this.imageFiles[field.key]);
      }

      return this.form[field.key] || null;
    },
    prepareFormData(payload) {
      const formData = new FormData();

      Object.entries(payload).forEach(([key, value]) => {
        if (value === null || value === undefined) {
          return;
        }

        if (typeof value === 'boolean') {
          formData.append(key, value ? '1' : '0');
        } else if (Array.isArray(value)) {
          formData.append(key, JSON.stringify(value));
        } else if (typeof value === 'object') {
          formData.append(key, JSON.stringify(value));
        } else {
          formData.append(key, value);
        }
      });

      this.config.formFields
        .filter((field) => field.type === 'image' || field.type === 'banner-image')
        .forEach((field) => {
          const file = this.imageFiles[field.key];

          if (file) {
            formData.append(field.fileKey || field.key, file);
          }
        });

      return formData;
    },
    async saveRecord() {
      this.saving = true;
      this.formError = null;
      try {
        const payload = this.preparePayload();
        const useFormData = this.hasFileUploadFields;

        if (this.editingId) {
          if (useFormData) {
            const formData = this.prepareFormData(payload);
            formData.append('_method', 'PUT');
            await window.axios.post(`${this.config.endpoint}/${this.editingId}`, formData);
          } else {
            await window.axios.put(`${this.config.endpoint}/${this.editingId}`, payload);
          }
        } else if (useFormData) {
          await window.axios.post(this.config.endpoint, this.prepareFormData(payload));
        } else {
          await window.axios.post(this.config.endpoint, payload);
        }
        this.hideModal(this.formModalId);
        await this.fetchRows();
      } catch (err) {
        if (err.message?.startsWith('Invalid JSON')) {
          this.formError = err.message;
        } else if (err.response?.data?.errors) {
          this.formError = Object.values(err.response.data.errors).flat().join(' ');
        } else {
          this.formError = err.response?.data?.message || 'Save failed.';
        }
      } finally {
        this.saving = false;
      }
    },
    confirmDelete(row) {
      this.confirmAction = () => window.axios.delete(`${this.config.endpoint}/${row.id}`);
      this.$refs.confirmModal.open(
        'Delete Record',
        `Delete this ${this.config.titleSingular || 'record'}?`,
      );
    },
    confirmForceDelete(row) {
      this.confirmAction = () => window.axios.delete(`${this.config.endpoint}/${row.id}/force`);
      this.$refs.confirmModal.open('Permanently Delete', 'This action cannot be undone.');
    },
    async restoreRow(row) {
      await window.axios.post(`${this.config.endpoint}/${row.id}/restore`);
      await this.fetchRows();
    },
    async executeConfirm() {
      this.saving = true;
      try {
        await this.confirmAction();
        this.hideModal(this.confirmModalId);
        await this.fetchRows();
      } finally {
        this.saving = false;
      }
    },
    openExport() {
      this.$refs.importExportModal.open('export');
    },
    openImport() {
      this.$refs.importExportModal.open('import');
    },
  },
};
</script>
