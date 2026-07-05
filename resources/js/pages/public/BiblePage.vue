<template>
  <div class="public-page bible-page">
    <h1 class="page-title">Holy Bible</h1>

    <div v-if="loading" class="text-center py-5">
      <i class="fas fa-spinner fa-spin fa-2x" />
    </div>

    <template v-else>
      <div class="bible-select-bars">
        <div class="bible-version-bar">
          <label for="bible-language-select" class="bible-version-label">Language</label>
          <select
            id="bible-language-select"
            v-model="selectedLanguage"
            class="form-select bible-version-select"
            @change="onLanguageChange"
          >
            <option v-for="language in languages" :key="language.code" :value="language.code">
              {{ language.label }}
            </option>
          </select>
        </div>

        <div class="bible-version-bar">
          <label for="bible-version-select" class="bible-version-label">Translation</label>
          <select
            id="bible-version-select"
            v-model="selectedVersionId"
            class="form-select bible-version-select"
            :disabled="!filteredVersions.length"
            @change="loadBooks"
          >
            <option v-for="version in filteredVersions" :key="version.id" :value="version.id">
              {{ version.name }} ({{ version.abbreviation }})
            </option>
          </select>
        </div>

        <div class="bible-version-bar">
          <label for="bible-testament-select" class="bible-version-label">Testament</label>
          <select
            id="bible-testament-select"
            v-model="selectedTestament"
            class="form-select bible-version-select"
          >
            <option value="old">Old Testament</option>
            <option value="new">New Testament</option>
          </select>
        </div>
      </div>

      <section v-if="selectedVersion && filteredBooks.length" class="bible-books-section">
        <h2 class="section-title">{{ booksSectionTitle }}</h2>
        <p class="text-muted bible-books-hint mb-3">Choose a book to begin reading.</p>
        <div class="book-grid book-grid--compact">
          <router-link
            v-for="book in filteredBooks"
            :key="book.id"
            :to="bookLink(book)"
            class="book-link"
          >
            {{ book.name }}
          </router-link>
        </div>
      </section>

      <p v-else-if="selectedVersion && books.length" class="text-muted mt-3">
        No books are available for this testament yet.
      </p>

      <p v-else-if="!filteredVersions.length" class="text-muted mt-3">
        No translations are available for this language yet.
      </p>

      <p v-else-if="selectedVersion && !books.length" class="text-muted mt-3">
        No books are available for this translation yet.
      </p>
    </template>
  </div>
</template>

<script>
export default {
  name: 'BiblePage',
  data() {
    return {
      languages: [],
      versions: [],
      books: [],
      selectedLanguage: 'en',
      selectedVersionId: null,
      selectedTestament: 'old',
      loading: true,
    };
  },
  computed: {
    filteredVersions() {
      return this.versions.filter((version) => version.language === this.selectedLanguage);
    },
    selectedVersion() {
      return this.filteredVersions.find((version) => version.id === this.selectedVersionId) || null;
    },
    filteredBooks() {
      return this.books.filter((book) => book.testament === this.selectedTestament);
    },
    booksSectionTitle() {
      const testamentLabel = this.selectedTestament === 'new' ? 'New Testament' : 'Old Testament';
      return `${this.selectedVersion?.name || 'Bible'} — ${testamentLabel}`;
    },
  },
  mounted() {
    this.loadLanguages();
  },
  methods: {
    async loadLanguages() {
      try {
        const [{ data: languages }, { data: versions }] = await Promise.all([
          window.axios.get('/public/bible/languages'),
          window.axios.get('/public/bible/versions'),
        ]);

        this.languages = languages;
        this.versions = versions;

        if (languages.length) {
          this.selectedLanguage = languages[0].code;
        }

        this.syncSelectedVersion();

        if (this.selectedVersionId) {
          await this.loadBooks();
        }
      } finally {
        this.loading = false;
      }
    },
    syncSelectedVersion() {
      const available = this.filteredVersions;

      if (!available.length) {
        this.selectedVersionId = null;
        this.books = [];
        return;
      }

      if (!available.some((version) => version.id === this.selectedVersionId)) {
        this.selectedVersionId = available[0].id;
      }
    },
    async onLanguageChange() {
      this.syncSelectedVersion();

      if (this.selectedVersionId) {
        await this.loadBooks();
      } else {
        this.books = [];
      }
    },
    async loadBooks() {
      if (!this.selectedVersion) {
        this.books = [];
        return;
      }

      const { data } = await window.axios.get(`/public/bible/${this.selectedVersion.abbreviation}/books`);
      this.books = data;
    },
    bookLink(book) {
      return `/bible/${this.selectedVersion.abbreviation}/${book.abbreviation || book.id}/1`;
    },
  },
};
</script>
