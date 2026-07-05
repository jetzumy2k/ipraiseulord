<template>
  <div class="public-page">
    <PageStaticHeader
      default-title="AI Spiritual Advice"
      default-intro="Ask a question and receive guidance rooted in scripture."
      update-seo
    />

    <form class="content-card mb-4" @submit.prevent="ask">
      <div class="mb-3">
        <label for="question" class="form-label">Your Question</label>
        <textarea
          id="question"
          v-model="question"
          class="form-control"
          rows="4"
          required
          placeholder="What does the Bible say about..."
        />
      </div>
      <div class="mb-3">
        <label for="bible-version" class="form-label">Preferred Bible Version (optional)</label>
        <input id="bible-version" v-model="bibleVersion" type="text" class="form-control" placeholder="e.g. KJV">
      </div>
      <button type="submit" class="btn btn-primary" :disabled="loading">
        <i v-if="loading" class="fas fa-spinner fa-spin me-1" />
        Ask
      </button>
    </form>

    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <article v-if="displaySections.length" class="content-card ai-advice-answer">
      <h2 class="ai-advice-title">Answer</h2>

      <div class="ai-advice-body">
        <template v-for="(section, index) in displaySections" :key="index">
          <p v-if="section.type === 'intro'" class="ai-answer-intro">{{ section.text }}</p>

          <p v-else-if="section.type === 'paragraph'" class="ai-answer-paragraph">{{ section.text }}</p>

          <blockquote v-else-if="section.type === 'scripture'" class="ai-answer-scripture">
            <p v-if="section.label" class="ai-answer-scripture-label">{{ section.label }}</p>
            <p class="ai-answer-scripture-text">{{ section.text }}</p>
          </blockquote>

          <p v-else-if="section.type === 'closing'" class="ai-answer-closing">{{ section.text }}</p>

          <p v-else class="ai-answer-paragraph">{{ section.text }}</p>
        </template>
      </div>

      <section v-if="uniqueReferences.length" class="ai-references">
        <h3 class="ai-references-title">Scripture References</h3>
        <ul class="ai-references-list">
          <li v-for="(ref, idx) in uniqueReferences" :key="idx" class="ai-reference-item">
            <p class="ai-reference-citation">{{ formatRef(ref) }}</p>
            <p v-if="ref.text" class="ai-reference-text">{{ ref.text }}</p>
          </li>
        </ul>
      </section>
    </article>
  </div>
</template>

<script>
import PageStaticHeader from '../../components/shared/PageStaticHeader.vue';
import { useVisitor } from '../../composables/useVisitor';

export default {
  name: 'AiAdvicePage',
  components: { PageStaticHeader },
  data() {
    return {
      question: '',
      bibleVersion: '',
      answer: '',
      answerSections: [],
      references: [],
      loading: false,
      error: null,
    };
  },
  computed: {
    displaySections() {
      if (this.answerSections.length) {
        return this.answerSections;
      }

      if (!this.answer) {
        return [];
      }

      return this.answer
        .split(/\n\s*\n/)
        .map((text) => text.trim())
        .filter(Boolean)
        .map((text) => ({ type: 'paragraph', text }));
    },
    uniqueReferences() {
      const seen = new Set();

      return this.references.filter((ref) => {
        const key = this.formatRef(ref).toLowerCase();
        if (seen.has(key)) {
          return false;
        }
        seen.add(key);
        return true;
      });
    },
  },
  methods: {
    async ask() {
      this.loading = true;
      this.error = null;
      this.answer = '';
      this.answerSections = [];
      this.references = [];
      const { visitorId } = useVisitor();

      try {
        const { data } = await window.axios.post('/public/ai/ask', {
          question: this.question,
          visitor_id: visitorId,
          bible_version: this.bibleVersion || null,
        });
        this.answer = data.answer;
        this.answerSections = data.answer_sections || [];
        this.references = data.references || [];
      } catch (err) {
        this.error = err.response?.data?.message || 'Unable to get a response. Please try again.';
      } finally {
        this.loading = false;
      }
    },
    formatRef(ref) {
      if (typeof ref === 'string') {
        return ref;
      }

      const reference = ref.reference || '';
      const version = ref.version ? ` (${ref.version})` : '';

      return `${reference}${version}`.trim() || ref.text || JSON.stringify(ref);
    },
  },
};
</script>
