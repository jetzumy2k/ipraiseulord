<template>
  <div class="social-share-bar" :class="{ 'social-share-bar--compact': compact }">
    <span v-if="!compact" class="social-share-label">Share</span>
    <div class="social-share-buttons">
      <button
        v-if="supportsNativeShare"
        type="button"
        class="social-share-btn social-share-btn--native"
        title="Share"
        @click.stop.prevent="shareNative"
      >
        <i class="fas fa-share-alt" />
        <span v-if="!compact">Share</span>
      </button>
      <button
        v-for="network in networks"
        :key="network.id"
        type="button"
        class="social-share-btn"
        :style="{ '--share-color': network.color }"
        :title="`Share on ${network.label}`"
        @click.stop.prevent="share(network.id)"
      >
        <i :class="network.icon" />
        <span v-if="!compact">{{ network.label }}</span>
      </button>
    </div>
    <p v-if="copied" class="social-share-feedback">Link copied to clipboard.</p>
  </div>
</template>

<script>
import { nativeShare, shareNetworks, shareToNetwork } from '../../utils/socialShare';

export default {
  name: 'SocialShareBar',
  props: {
    title: { type: String, default: '' },
    text: { type: String, default: '' },
    url: { type: String, default: '' },
    compact: { type: Boolean, default: false },
  },
  data() {
    return {
      copied: false,
      sharing: false,
      networks: shareNetworks,
      supportsNativeShare: typeof navigator !== 'undefined' && !!navigator.share,
    };
  },
  methods: {
    payload() {
      return {
        title: this.title,
        text: this.text,
        url: this.url != null && this.url !== '' ? this.url : window.location.href,
      };
    },
    async share(networkId) {
      if (this.sharing) {
        return;
      }

      this.sharing = true;

      let success = false;

      try {
        success = await shareToNetwork(networkId, this.payload());
      } finally {
        window.setTimeout(() => {
          this.sharing = false;
        }, 800);
      }
      if (networkId === 'copy' && success) {
        this.copied = true;
        window.setTimeout(() => {
          this.copied = false;
        }, 2500);
      }
    },
    async shareNative() {
      if (this.sharing) {
        return;
      }

      this.sharing = true;

      try {
        await nativeShare(this.payload());
      } finally {
        window.setTimeout(() => {
          this.sharing = false;
        }, 800);
      }
    },
  },
};
</script>
