<template>
  <div class="public-page">
    <nav class="breadcrumb-nav mb-3">
      <router-link to="/bible">Bible</router-link>
      <span class="mx-2">/</span>
      <span>{{ version }}</span>
      <span class="mx-2">/</span>
      <span>{{ bookName }}</span>
    </nav>

    <form class="bible-jump-nav content-card mb-4" @submit.prevent="goToSelection">
      <div class="bible-jump-nav-fields">
        <label class="bible-jump-field">
          <span class="bible-jump-label">Language</span>
          <select
            v-model="nav.language"
            class="form-select form-select-sm"
            :disabled="navLoading"
            @change="onLanguageChange"
          >
            <option v-for="item in languages" :key="item.code" :value="item.code">
              {{ item.label }}
            </option>
          </select>
        </label>

        <label class="bible-jump-field">
          <span class="bible-jump-label">Translation</span>
          <select
            v-model="nav.version"
            class="form-select form-select-sm"
            :disabled="navLoading || !filteredVersions.length"
            @change="onVersionChange"
          >
            <option v-for="item in filteredVersions" :key="item.id" :value="item.abbreviation">
              {{ item.abbreviation }} — {{ item.name }}
            </option>
          </select>
        </label>

        <label class="bible-jump-field">
          <span class="bible-jump-label">Book</span>
          <select
            v-model="nav.book"
            class="form-select form-select-sm"
            :disabled="navLoading || !books.length"
            @change="onBookChange"
          >
            <option v-for="item in books" :key="item.id" :value="bookSlug(item)">
              {{ item.name }}
            </option>
          </select>
        </label>

        <label class="bible-jump-field bible-jump-field--short">
          <span class="bible-jump-label">Chapter</span>
          <select
            v-model.number="nav.chapter"
            class="form-select form-select-sm"
            :disabled="navLoading || !chapters.length"
            @change="onChapterChange"
          >
            <option v-for="num in chapters" :key="num" :value="num">
              {{ num }}
            </option>
          </select>
        </label>

        <label class="bible-jump-field bible-jump-field--short">
          <span class="bible-jump-label">Verse</span>
          <select
            v-model.number="nav.verse"
            class="form-select form-select-sm"
            :disabled="navLoading || !verses.length"
          >
            <option v-for="num in verses" :key="num" :value="num">
              {{ num }}
            </option>
          </select>
        </label>

        <button type="submit" class="btn btn-sm btn-primary bible-jump-go" :disabled="navLoading">
          Go
        </button>
      </div>
    </form>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
      <h1 class="page-title mb-0">Chapter {{ chapter }}</h1>
      <div class="d-flex align-items-center gap-2">
        <p v-if="selectedVerseNumbers.length" class="bible-selection-hint mb-0">
          {{ selectedVerseNumbers.length }} verse{{ selectedVerseNumbers.length === 1 ? '' : 's' }} selected
          <button type="button" class="btn btn-link btn-sm p-0 ms-1" @click="clearSelection">Clear</button>
        </p>
        <div class="chapter-nav btn-group">
          <router-link
            v-if="chapterData?.prev_chapter"
            :to="chapterLink(chapterData.prev_chapter)"
            class="btn btn-sm btn-outline-primary"
          >
            &larr; Prev
          </router-link>
          <router-link
            v-if="chapterData?.next_chapter"
            :to="chapterLink(chapterData.next_chapter)"
            class="btn btn-sm btn-outline-primary"
          >
            Next &rarr;
          </router-link>
        </div>
      </div>
    </div>

    <p class="bible-select-help text-muted small mb-3">
      Click verses to select them, or highlight text with your mouse. Share buttons appear when one or more verses are selected.
    </p>

    <div v-if="loading" class="text-center py-5">
      <i class="fas fa-spinner fa-spin fa-2x" />
    </div>

    <div
      v-else
      ref="scriptureText"
      class="scripture-text scripture-text--selectable"
      @mouseup="onScriptureMouseUp"
    >
      <p
        v-for="verse in versesList"
        :id="`v${verse.verse_number}`"
        :key="verse.id"
        class="verse-line"
        :class="{ 'verse-line--selected': isVerseSelected(verse.verse_number) }"
        @click="onVerseClick(verse, $event)"
      >
        <sup class="verse-num">{{ verse.verse_number }}</sup>
        <span class="verse-text">{{ verse.text }}</span>
      </p>
    </div>

    <VerseShareToolbar
      :visible="showShareToolbar"
      :title="verseShareTitle"
      :text="verseShareText"
      :url="verseShareUrl"
      :selection-label="verseSelectionLabel"
      @clear="clearSelection"
    />
  </div>
</template>

<script>
import VerseShareToolbar from '../../components/shared/VerseShareToolbar.vue';
import { bibleVerseShareUrl, buildBibleVerseShareText } from '../../utils/socialShare';
import { applySeo, buildWebPageJsonLd } from '../../utils/seo';

export default {
  name: 'BibleChapterPage',
  components: { VerseShareToolbar },
  data() {
    return {
      chapterData: null,
      loading: true,
      languages: [],
      versions: [],
      books: [],
      verseCount: 0,
      navLoading: false,
      booksVersion: null,
      selectedVerseNumbers: [],
      lastSelectedVerse: null,
      highlightedText: '',
      nav: {
        language: 'en',
        version: '',
        book: '',
        chapter: 1,
        verse: 1,
      },
    };
  },
  computed: {
    version() {
      return this.$route.params.version;
    },
    book() {
      return this.$route.params.book;
    },
    chapter() {
      return this.$route.params.chapter;
    },
    bookName() {
      return this.chapterData?.book?.name || this.selectedBook?.name || this.book;
    },
    versesList() {
      return this.chapterData?.verses || [];
    },
    selectedBook() {
      return this.books.find((item) => this.bookSlug(item) === this.nav.book) || null;
    },
    filteredVersions() {
      return this.versions.filter((item) => item.language === this.nav.language);
    },
    chapters() {
      const count = this.selectedBook?.chapter_count || 0;
      return count ? Array.from({ length: count }, (_, index) => index + 1) : [];
    },
    verses() {
      return this.verseCount
        ? Array.from({ length: this.verseCount }, (_, index) => index + 1)
        : [];
    },
    selectedVerses() {
      return this.versesList.filter((verse) => this.selectedVerseNumbers.includes(verse.verse_number));
    },
    showShareToolbar() {
      return this.selectedVerseNumbers.length > 0;
    },
    verseShareMeta() {
      return buildBibleVerseShareText({
        bookName: this.bookName,
        chapter: this.chapter,
        version: this.version,
        verses: this.selectedVerses,
      });
    },
    verseShareTitle() {
      return this.verseShareMeta.title;
    },
    verseShareText() {
      if (this.highlightedText && this.selectedVerses.length === 1) {
        return `"${this.highlightedText.trim()}"\n\n— ${this.verseShareMeta.reference} (${this.version})`;
      }

      return this.verseShareMeta.text;
    },
    verseShareUrl() {
      return bibleVerseShareUrl(this.$route.path, this.selectedVerses);
    },
    verseSelectionLabel() {
      const count = this.selectedVerseNumbers.length;
      return count === 1 ? '1 verse selected' : `${count} verses selected`;
    },
  },
  watch: {
    '$route.params': {
      immediate: true,
      handler() {
        this.clearSelection();
        this.loadChapter();
      },
    },
    '$route.hash'() {
      this.syncVerseFromHash();
      this.scrollToVerse();
    },
  },
  methods: {
    bookSlug(book) {
      return book.abbreviation || String(book.id);
    },
    isVerseSelected(verseNumber) {
      return this.selectedVerseNumbers.includes(verseNumber);
    },
    onVerseClick(verse, event) {
      const selection = window.getSelection();
      if (selection && !selection.isCollapsed && selection.toString().trim()) {
        return;
      }

      const verseNumber = verse.verse_number;

      if (event.shiftKey && this.lastSelectedVerse != null) {
        const start = Math.min(this.lastSelectedVerse, verseNumber);
        const end = Math.max(this.lastSelectedVerse, verseNumber);
        const range = [];

        for (let num = start; num <= end; num += 1) {
          if (this.versesList.some((item) => item.verse_number === num)) {
            range.push(num);
          }
        }

        this.selectedVerseNumbers = [...new Set([...this.selectedVerseNumbers, ...range])].sort((a, b) => a - b);
      } else if (this.isVerseSelected(verseNumber)) {
        this.selectedVerseNumbers = this.selectedVerseNumbers.filter((num) => num !== verseNumber);
      } else {
        this.selectedVerseNumbers = [...this.selectedVerseNumbers, verseNumber].sort((a, b) => a - b);
      }

      this.lastSelectedVerse = verseNumber;
      this.highlightedText = '';
    },
    onScriptureMouseUp() {
      this.$nextTick(() => {
        const selection = window.getSelection();
        if (!selection || selection.isCollapsed) {
          return;
        }

        const text = selection.toString().trim();
        if (text.length < 2) {
          return;
        }

        const container = this.$refs.scriptureText;
        if (!container || !container.contains(selection.anchorNode)) {
          return;
        }

        const verseNumbers = this.getVerseNumbersFromSelection(selection);
        if (!verseNumbers.length) {
          return;
        }

        this.selectedVerseNumbers = verseNumbers;
        this.highlightedText = text;
        this.lastSelectedVerse = verseNumbers[verseNumbers.length - 1];
      });
    },
    getVerseNumbersFromSelection(selection) {
      const numbers = new Set();
      const container = this.$refs.scriptureText;

      for (let index = 0; index < selection.rangeCount; index += 1) {
        const range = selection.getRangeAt(index);
        container.querySelectorAll('.verse-line').forEach((element) => {
          if (range.intersectsNode(element)) {
            const match = element.id.match(/^v(\d+)$/);
            if (match) {
              numbers.add(Number(match[1]));
            }
          }
        });
      }

      return [...numbers].sort((a, b) => a - b);
    },
    clearSelection() {
      this.selectedVerseNumbers = [];
      this.lastSelectedVerse = null;
      this.highlightedText = '';
      window.getSelection()?.removeAllRanges();
    },
    async loadChapter() {
      this.loading = true;

      try {
        const { data } = await window.axios.get(
          `/public/bible/${this.version}/${this.book}/${this.chapter}`,
        );
        this.chapterData = data;
        this.updateSeo();
        await this.syncNavigation();
        this.syncVerseFromHash();
        this.scrollToVerse();
      } finally {
        this.loading = false;
      }
    },
    updateSeo() {
      if (!this.chapterData) {
        return;
      }

      const title = `${this.bookName} ${this.chapter} (${this.version})`;
      const firstVerse = this.versesList[0]?.text || '';
      const description = firstVerse
        ? `${firstVerse.slice(0, 140).trim()}…`
        : `Read ${this.bookName} chapter ${this.chapter} in the ${this.version} Bible translation online.`;
      const url = `${window.location.origin}${this.$route.path}`;

      applySeo({
        title,
        description,
        url,
        jsonLd: buildWebPageJsonLd({
          title,
          description,
          url,
          breadcrumbs: [
            { name: 'Bible', item: '/bible' },
            { name: this.bookName, item: `/bible/${this.version}/${this.book}` },
            { name: `Chapter ${this.chapter}`, item: this.$route.path },
          ],
        }),
      });
    },
    async syncNavigation() {
      this.nav.version = this.version;
      this.nav.book = this.book;
      this.nav.chapter = Number(this.chapter);

      if (!this.versions.length) {
        const [{ data: languages }, { data: versions }] = await Promise.all([
          window.axios.get('/public/bible/languages'),
          window.axios.get('/public/bible/versions'),
        ]);
        this.languages = languages;
        this.versions = versions;
      }

      const currentVersion = this.versions.find((item) => item.abbreviation === this.nav.version);
      this.nav.language = currentVersion?.language || this.languages[0]?.code || 'en';
      this.syncNavVersion();

      if (this.booksVersion !== this.nav.version) {
        await this.loadBooks(this.nav.version);
      }

      if (!this.books.some((item) => this.bookSlug(item) === this.nav.book)) {
        const fallback = this.books[0];
        if (fallback) {
          this.nav.book = this.bookSlug(fallback);
        }
      }

      await this.loadVerseMeta();
    },
    async loadBooks(version) {
      this.navLoading = true;

      try {
        const { data } = await window.axios.get(`/public/bible/${version}/books`);
        this.books = data;
        this.booksVersion = version;
      } finally {
        this.navLoading = false;
      }
    },
    async loadVerseMeta() {
      if (!this.nav.version || !this.nav.book || !this.nav.chapter) {
        this.verseCount = 0;
        return;
      }

      this.navLoading = true;

      try {
        const { data } = await window.axios.get(
          `/public/bible/${this.nav.version}/${this.nav.book}/${this.nav.chapter}/meta`,
        );
        this.verseCount = data.verse_count || 0;

        if (this.nav.verse > this.verseCount) {
          this.nav.verse = 1;
        } else if (this.nav.verse < 1) {
          this.nav.verse = 1;
        }
      } catch {
        this.verseCount = 0;
        this.nav.verse = 1;
      } finally {
        this.navLoading = false;
      }
    },
    syncVerseFromHash() {
      const match = this.$route.hash.match(/^#v(\d+)(?:-v(\d+))?$/);

      if (!match) {
        return;
      }

      const start = Number(match[1]);
      const end = match[2] ? Number(match[2]) : start;
      const numbers = [];

      for (let num = Math.min(start, end); num <= Math.max(start, end); num += 1) {
        numbers.push(num);
      }

      this.selectedVerseNumbers = numbers;
      this.lastSelectedVerse = end;
    },
    syncNavVersion() {
      const available = this.filteredVersions;

      if (!available.length) {
        return;
      }

      if (!available.some((item) => item.abbreviation === this.nav.version)) {
        this.nav.version = available[0].abbreviation;
      }
    },
    async onLanguageChange() {
      this.syncNavVersion();
      await this.loadBooks(this.nav.version);

      const currentBook = this.books.find((item) => this.bookSlug(item) === this.nav.book);
      if (!currentBook && this.books.length) {
        this.nav.book = this.bookSlug(this.books[0]);
      }

      this.nav.chapter = 1;
      this.nav.verse = 1;
      await this.loadVerseMeta();
    },
    async onVersionChange() {
      await this.loadBooks(this.nav.version);

      const currentBook = this.books.find((item) => this.bookSlug(item) === this.nav.book);
      if (!currentBook && this.books.length) {
        this.nav.book = this.bookSlug(this.books[0]);
      }

      this.nav.chapter = 1;
      this.nav.verse = 1;
      await this.loadVerseMeta();
    },
    async onBookChange() {
      this.nav.chapter = 1;
      this.nav.verse = 1;
      await this.loadVerseMeta();
    },
    async onChapterChange() {
      this.nav.verse = 1;
      await this.loadVerseMeta();
    },
    goToSelection() {
      const path = `/bible/${this.nav.version}/${this.nav.book}/${this.nav.chapter}`;
      const hash = this.nav.verse ? `#v${this.nav.verse}` : '';

      if (
        this.$route.path === path
        && this.$route.hash === hash
      ) {
        this.scrollToVerse();
        return;
      }

      this.$router.push({ path, hash });
    },
    scrollToVerse() {
      this.$nextTick(() => {
        const match = this.$route.hash.match(/^#v(\d+)/);
        if (!match) {
          return;
        }

        const element = document.getElementById(`v${match[1]}`);
        element?.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    },
    chapterLink(num) {
      return `/bible/${this.version}/${this.book}/${num}`;
    },
  },
};
</script>
