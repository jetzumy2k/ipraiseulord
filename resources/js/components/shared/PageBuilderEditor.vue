<template>
  <div class="page-builder">
    <div class="page-builder-toolbar">
      <div class="dropdown">
        <button
          type="button"
          class="btn btn-sm btn-primary dropdown-toggle"
          data-bs-toggle="dropdown"
        >
          <i class="fas fa-plus me-1" /> Add Block
        </button>
        <ul class="dropdown-menu">
          <li v-for="item in blockTypes" :key="item.type">
            <button type="button" class="dropdown-item" @click="addBlock(item.type)">
              <i :class="item.icon" class="me-2" />{{ item.label }}
            </button>
          </li>
        </ul>
      </div>
      <span class="text-muted small">{{ blocks.length }} block{{ blocks.length === 1 ? '' : 's' }}</span>
    </div>

    <div v-if="!blocks.length" class="page-builder-empty">
      <p class="mb-2">Start building your page with content blocks.</p>
      <button type="button" class="btn btn-sm btn-outline-primary" @click="addBlock('paragraph')">
        Add Paragraph
      </button>
    </div>

    <div v-for="(block, index) in blocks" :key="block.id" class="page-builder-block">
      <div class="page-builder-block-header">
        <span class="page-builder-block-label">
          <i :class="blockIcon(block.type)" class="me-1" />
          {{ blockLabel(block.type) }}
        </span>
        <div class="btn-group btn-group-sm">
          <button type="button" class="btn btn-outline-secondary" :disabled="index === 0" @click="moveBlock(index, -1)">
            <i class="fas fa-arrow-up" />
          </button>
          <button type="button" class="btn btn-outline-secondary" :disabled="index === blocks.length - 1" @click="moveBlock(index, 1)">
            <i class="fas fa-arrow-down" />
          </button>
          <button type="button" class="btn btn-outline-danger" @click="removeBlock(index)">
            <i class="fas fa-trash" />
          </button>
        </div>
      </div>

      <div class="page-builder-block-body">
        <template v-if="block.type === 'heading'">
          <div class="row g-2">
            <div class="col-md-3">
              <label class="form-label small">Level</label>
              <select v-model.number="block.level" class="form-select form-select-sm">
                <option v-for="level in [1, 2, 3, 4, 5, 6]" :key="level" :value="level">H{{ level }}</option>
              </select>
            </div>
            <div class="col-md-9">
              <label class="form-label small">Heading text</label>
              <input v-model="block.text" type="text" class="form-control form-control-sm" placeholder="Section heading">
            </div>
          </div>
        </template>

        <template v-else-if="block.type === 'paragraph'">
          <RichTextEditor v-model="block.html" :editor-key="block.id" compact />
        </template>

        <template v-else-if="block.type === 'quote'">
          <RichTextEditor v-model="block.html" :editor-key="`${block.id}-quote`" compact placeholder="Quote text..." />
        </template>

        <template v-else-if="block.type === 'list'">
          <div class="mb-2">
            <label class="form-check form-check-inline">
              <input v-model="block.ordered" class="form-check-input" type="checkbox">
              <span class="form-check-label">Numbered list</span>
            </label>
          </div>
          <div v-for="(item, itemIndex) in block.items" :key="itemIndex" class="input-group input-group-sm mb-2">
            <span class="input-group-text">{{ itemIndex + 1 }}</span>
            <input v-model="block.items[itemIndex]" type="text" class="form-control" placeholder="List item">
            <button type="button" class="btn btn-outline-danger" @click="removeListItem(block, itemIndex)">
              <i class="fas fa-times" />
            </button>
          </div>
          <button type="button" class="btn btn-sm btn-outline-secondary" @click="addListItem(block)">
            Add item
          </button>
        </template>

        <template v-else-if="block.type === 'image'">
          <div class="row g-2">
            <div class="col-12">
              <label class="form-label small">Image URL</label>
              <input v-model="block.url" type="url" class="form-control form-control-sm" placeholder="https://...">
            </div>
            <div class="col-md-6">
              <label class="form-label small">Alt text</label>
              <input v-model="block.alt" type="text" class="form-control form-control-sm">
            </div>
            <div class="col-md-6">
              <label class="form-label small">Caption</label>
              <input v-model="block.caption" type="text" class="form-control form-control-sm">
            </div>
          </div>
        </template>

        <template v-else-if="block.type === 'separator'">
          <hr class="page-builder-separator-preview">
        </template>

        <template v-else-if="block.type === 'columns'">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small">Left column</label>
              <RichTextEditor v-model="block.leftHtml" :editor-key="`${block.id}-left`" compact />
            </div>
            <div class="col-md-6">
              <label class="form-label small">Right column</label>
              <RichTextEditor v-model="block.rightHtml" :editor-key="`${block.id}-right`" compact />
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
import RichTextEditor from './RichTextEditor.vue';
import { blockTypes, blocksToHtml, createBlock, htmlToBlocks } from '../../utils/pageBuilder';

export default {
  name: 'PageBuilderEditor',
  components: { RichTextEditor },
  props: {
    modelValue: { type: String, default: '' },
  },
  emits: ['update:modelValue'],
  data() {
    return {
      blocks: htmlToBlocks(this.modelValue),
      blockTypes,
      syncing: false,
    };
  },
  watch: {
    modelValue(value) {
      if (this.syncing) {
        return;
      }

      this.blocks = htmlToBlocks(value);
    },
    blocks: {
      deep: true,
      handler(blocks) {
        this.syncing = true;
        this.$emit('update:modelValue', blocksToHtml(blocks));
        this.$nextTick(() => {
          this.syncing = false;
        });
      },
    },
  },
  methods: {
    addBlock(type) {
      this.blocks.push(createBlock(type));
    },
    removeBlock(index) {
      this.blocks.splice(index, 1);
    },
    moveBlock(index, direction) {
      const target = index + direction;
      if (target < 0 || target >= this.blocks.length) {
        return;
      }

      const [block] = this.blocks.splice(index, 1);
      this.blocks.splice(target, 0, block);
    },
    addListItem(block) {
      block.items.push('');
    },
    removeListItem(block, index) {
      block.items.splice(index, 1);
      if (!block.items.length) {
        block.items.push('');
      }
    },
    blockLabel(type) {
      return blockTypes.find((item) => item.type === type)?.label || type;
    },
    blockIcon(type) {
      return blockTypes.find((item) => item.type === type)?.icon || 'fas fa-square';
    },
  },
};
</script>
