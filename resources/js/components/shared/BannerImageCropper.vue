<template>
  <AppModal
    :modal-id="modalId"
    title="Crop Banner Image"
    size="xl"
  >
    <div class="banner-cropper">
      <p class="text-muted small mb-3">
        Crop your image to the banner size ({{ width }} × {{ height }} px). The overlay keeps text readable on the public site.
      </p>
      <div class="banner-cropper__stage">
        <img
          v-if="imageSrc"
          ref="image"
          :key="imageSrc"
          :src="imageSrc"
          alt="Banner crop preview"
          @load="onImageLoad"
        >
      </div>
    </div>
    <template #footer>
      <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
      <button type="button" class="btn btn-primary" :disabled="processing || !cropperReady" @click="applyCrop">
        <i v-if="processing" class="fas fa-spinner fa-spin me-1" />
        Apply Crop
      </button>
    </template>
  </AppModal>
</template>

<script>
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';
import AppModal from './AppModal.vue';
import { BANNER_ASPECT_RATIO, BANNER_HEIGHT, BANNER_WIDTH } from '../../config/banner';

export default {
  name: 'BannerImageCropper',
  components: { AppModal },
  props: {
    modalId: {
      type: String,
      default: 'banner-image-cropper-modal',
    },
  },
  emits: ['cropped', 'cancelled'],
  data() {
    return {
      imageSrc: '',
      sourceFile: null,
      cropper: null,
      processing: false,
      cropperReady: false,
      pendingInit: false,
      modalVisible: false,
      imageLoaded: false,
      width: BANNER_WIDTH,
      height: BANNER_HEIGHT,
    };
  },
  beforeUnmount() {
    const element = document.getElementById(this.modalId);
    element?.removeEventListener('hidden.bs.modal', this.onModalHidden);
    element?.removeEventListener('shown.bs.modal', this.onModalShown);
    this.cleanup();
  },
  mounted() {
    const element = document.getElementById(this.modalId);
    element?.addEventListener('hidden.bs.modal', this.onModalHidden);
    element?.addEventListener('shown.bs.modal', this.onModalShown);
  },
  methods: {
    open(file) {
      this.cleanup();
      this.sourceFile = file;
      this.imageSrc = URL.createObjectURL(file);
      this.processing = false;
      this.cropperReady = false;
      this.pendingInit = true;
      this.modalVisible = false;
      this.imageLoaded = false;
      this.showModal();
    },
    showModal() {
      const element = document.getElementById(this.modalId);

      if (element && window.bootstrap?.Modal) {
        window.bootstrap.Modal.getOrCreateInstance(element).show();
      }
    },
    hideModal() {
      const element = document.getElementById(this.modalId);

      if (element && window.bootstrap?.Modal) {
        window.bootstrap.Modal.getOrCreateInstance(element).hide();
      }
    },
    onModalShown() {
      this.modalVisible = true;
      this.tryInitCropper();
    },
    onImageLoad() {
      this.imageLoaded = true;
      this.tryInitCropper();
    },
    tryInitCropper() {
      if (!this.pendingInit || !this.modalVisible || !this.imageLoaded || this.cropper) {
        return;
      }

      const image = this.$refs.image;

      if (!image || !this.imageSrc) {
        return;
      }

      this.initCropper(image);
    },
    initCropper(image) {
      if (!this.pendingInit || !image || !this.imageSrc || this.cropper) {
        return;
      }

      this.destroyCropperInstance();

      this.cropper = new Cropper(image, {
        aspectRatio: BANNER_ASPECT_RATIO,
        viewMode: 1,
        dragMode: 'move',
        autoCropArea: 1,
        responsive: true,
        background: false,
        guides: true,
        center: true,
        highlight: false,
        cropBoxMovable: true,
        cropBoxResizable: true,
        toggleDragModeOnDblclick: false,
        ready: () => {
          this.cropperReady = true;
          this.pendingInit = false;
        },
      });
    },
    destroyCropperInstance() {
      this.cropper?.destroy();
      this.cropper = null;
      this.cropperReady = false;
    },
    releaseImageUrl() {
      if (this.imageSrc) {
        URL.revokeObjectURL(this.imageSrc);
        this.imageSrc = '';
      }
    },
    cleanup() {
      this.pendingInit = false;
      this.modalVisible = false;
      this.imageLoaded = false;
      this.destroyCropperInstance();
      this.releaseImageUrl();
    },
    applyCrop() {
      if (!this.cropper || !this.sourceFile || !this.cropperReady) {
        return;
      }

      this.processing = true;

      const canvas = this.cropper.getCroppedCanvas({
        width: BANNER_WIDTH,
        height: BANNER_HEIGHT,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
      });

      if (!canvas) {
        this.processing = false;
        return;
      }

      canvas.toBlob((blob) => {
        this.processing = false;

        if (!blob) {
          return;
        }

        const extension = this.sourceFile.type === 'image/png' ? 'png' : 'jpg';
        const fileName = this.sourceFile.name.replace(/\.[^.]+$/, '') || 'banner';
        const croppedFile = new File([blob], `${fileName}-banner.${extension}`, {
          type: blob.type || 'image/jpeg',
          lastModified: Date.now(),
        });

        this.pendingInit = false;
        this.$emit('cropped', croppedFile);
        this.hideModal();
      }, this.sourceFile.type === 'image/png' ? 'image/png' : 'image/jpeg', 0.92);
    },
    onModalHidden() {
      this.cleanup();
      this.sourceFile = null;
      this.$emit('cancelled');
    },
  },
};
</script>

<style scoped>
.banner-cropper__stage {
  max-height: 60vh;
  overflow: hidden;
  background: #111;
}

.banner-cropper__stage img {
  display: block;
  max-width: 100%;
}
</style>
