<template>
  <div class="mb-3 form-captcha">
    <label :for="inputId" class="form-label">
      Security Check
      <span class="text-danger">*</span>
    </label>

    <div v-if="loading" class="form-captcha-loading text-muted small">
      <i class="fas fa-spinner fa-spin me-1" />
      Loading security question...
    </div>

    <template v-else>
      <div class="form-captcha-question" aria-live="polite">
        <span class="form-captcha-type">{{ typeLabel }}</span>
        <span>{{ question }}</span>
      </div>

      <div class="input-group">
        <input
          :id="inputId"
          :value="captchaAnswer"
          type="text"
          class="form-control"
          autocomplete="off"
          autocapitalize="off"
          spellcheck="false"
          required
          :disabled="disabled"
          placeholder="Your answer"
          @input="$emit('update:captchaAnswer', $event.target.value)"
        >
        <button
          type="button"
          class="btn btn-outline-secondary"
          title="Get a new question"
          :disabled="disabled || loading"
          @click="loadCaptcha"
        >
          <i class="fas fa-sync-alt" />
        </button>
      </div>

      <small class="text-muted d-block mt-1">
        Answer the question above to verify you are human.
      </small>
    </template>
  </div>
</template>

<script>
let captchaCounter = 0;

export default {
  name: 'FormCaptcha',
  props: {
    context: {
      type: String,
      required: true,
      validator: (value) => ['contact', 'login'].includes(value),
    },
    captchaId: {
      type: String,
      default: '',
    },
    captchaAnswer: {
      type: String,
      default: '',
    },
    disabled: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['update:captchaId', 'update:captchaAnswer'],
  data() {
    captchaCounter += 1;

    return {
      loading: true,
      question: '',
      type: '',
      inputId: `form-captcha-${captchaCounter}`,
    };
  },
  computed: {
    typeLabel() {
      if (this.type === 'math') return 'Math';
      if (this.type === 'spelling') return 'Spelling';
      if (this.type === 'blank') return 'Fill in the blank';

      return 'Captcha';
    },
  },
  mounted() {
    this.loadCaptcha();
  },
  methods: {
    async loadCaptcha() {
      this.loading = true;
      this.$emit('update:captchaAnswer', '');

      try {
        const { data } = await window.axios.get('/public/captcha', {
          params: { context: this.context },
        });

        this.question = data.question;
        this.type = data.type;
        this.$emit('update:captchaId', data.id);
      } catch {
        this.question = 'Unable to load security question. Click refresh to try again.';
        this.type = '';
        this.$emit('update:captchaId', '');
      } finally {
        this.loading = false;
      }
    },
    refresh() {
      return this.loadCaptcha();
    },
  },
};
</script>
