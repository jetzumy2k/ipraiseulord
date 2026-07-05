<template>
  <div
    v-if="visible"
    class="verse-share-toolbar"
    role="toolbar"
    aria-label="Share selected verses"
  >
    <div class="verse-share-toolbar-inner">
      <span class="verse-share-count">{{ selectionLabel }}</span>
      <div class="verse-share-buttons">
        <button
          v-if="supportsNativeShare"
          type="button"
          class="verse-share-btn"
          title="Share"
          @click="shareNative"
        >
          <i class="fas fa-share-alt" />
        </button>
        <button
          v-for="network in networks"
          :key="network.id"
          type="button"
          class="verse-share-btn"
          :style="{ '--share-color': network.color }"
          :title="network.id === 'copy' ? 'Copy link' : `Share on ${network.label}`"
          @click="share(network.id)"
        >
          <i :class="network.icon" />
        </button>
        <button type="button" class="verse-share-btn verse-share-btn--clear" title="Clear selection" @click="$emit('clear')">
          <i class="fas fa-times" />
        </button>
      </div>
      <p v-if="copied" class="verse-share-feedback">Copied!</p>
    </div>
  </div>
</template>

<script>
import { nativeShare, shareNetworks, shareToNetwork } from '../../utils/socialShare';

export default {
  name: 'VerseShareToolbar',
  props: {
    visible: { type: Boolean, default: false },
    title: { type: String, default: '' },
    text: { type: String, default: '' },
    url: { type: String, default: '' },
    selectionLabel: { type: String, default: '' },
  },
  emits: ['clear'],
  data() {
    return {
      copied: false,
      networks: shareNetworks,
      supportsNativeShare: typeof navigator !== 'undefined' && !!navigator.share,
    };
  },
  methods: {
    payload() {
      return {
        title: this.title,
        text: this.text,
        url: this.url,
      };
    },
    async share(networkId) {
      const success = await shareToNetwork(networkId, this.payload());
      if (networkId === 'copy' && success) {
        this.copied = true;
        window.setTimeout(() => {
          this.copied = false;
        }, 2000);
      }
    },
    shareNative() {
      nativeShare(this.payload());
    },
  },
};
</script>
