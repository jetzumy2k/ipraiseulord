<template>
  <div class="rich-text-editor" :class="{ 'rich-text-editor--compact': compact }">
    <QuillEditor
      :key="editorKey"
      v-model:content="localContent"
      content-type="html"
      theme="snow"
      :toolbar="toolbarOptions"
      :placeholder="placeholder"
      @update:content="onUpdate"
    />
  </div>
</template>

<script>
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

export default {
  name: 'RichTextEditor',
  components: { QuillEditor },
  props: {
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'Write here...' },
    compact: { type: Boolean, default: false },
    editorKey: { type: [String, Number], default: 'default' },
  },
  emits: ['update:modelValue'],
  data() {
    return {
      localContent: this.modelValue || '',
      toolbarOptions: [
        [{ header: [1, 2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ align: [] }],
        ['blockquote', 'link'],
        ['clean'],
      ],
    };
  },
  watch: {
    modelValue(value) {
      if ((value || '') !== (this.localContent || '')) {
        this.localContent = value || '';
      }
    },
  },
  methods: {
    onUpdate(value) {
      const html = value === '<p><br></p>' ? '' : (value || '');
      this.$emit('update:modelValue', html);
    },
  },
};
</script>
