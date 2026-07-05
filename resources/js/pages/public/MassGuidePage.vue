<template>
  <div class="public-page">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-2">
      <div>
        <h1 class="page-title mb-1">Mass Guide</h1>
        <p class="text-muted mb-0">Full order of Mass with priest and people responses.</p>
      </div>
      <div class="mass-guide-nav">
        <button
          type="button"
          class="btn btn-sm btn-outline-primary"
          :disabled="!mass?.prev_date || loading"
          aria-label="Previous day"
          @click="goToDate(mass.prev_date)"
        >
          <i class="fas fa-chevron-left" />
        </button>
        <input
          v-model="selectedDate"
          type="date"
          class="form-control form-control-sm mass-guide-date"
          :disabled="loading"
          @change="onDateChange"
        >
        <button
          type="button"
          class="btn btn-sm btn-outline-secondary"
          :disabled="loading || isToday"
          @click="goToday"
        >
          Today
        </button>
        <button
          type="button"
          class="btn btn-sm btn-outline-primary"
          :disabled="!mass?.next_date || loading"
          aria-label="Next day"
          @click="goToDate(mass.next_date)"
        >
          <i class="fas fa-chevron-right" />
        </button>
      </div>
    </div>

    <div v-if="loading" class="text-center py-5">
      <i class="fas fa-spinner fa-spin fa-2x" />
    </div>

    <div v-else-if="error" class="alert alert-info">{{ error }}</div>

    <article v-else class="content-card mass-guide">
      <header class="mb-4">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
          <div>
            <p class="liturgical-date">{{ formatDate(mass.liturgical_date) }}</p>
            <h2>{{ mass.feast_name || 'Daily Mass' }}</h2>
            <p v-if="mass.liturgical_season" class="text-muted mb-0">
              {{ mass.liturgical_season }}
              <span v-if="mass.liturgical_color"> · {{ mass.liturgical_color }}</span>
              <span v-if="mass.liturgical_year"> · Liturgical year {{ mass.liturgical_year }}</span>
            </p>
          </div>
          <SocialShareBar
            :title="shareTitle"
            :text="shareText"
            :url="shareUrl"
            compact
          />
        </div>
      </header>

      <nav v-if="sections.length" class="mass-guide-toc mb-4" aria-label="Mass sections">
        <a
          v-for="section in sections"
          :key="section.id"
          :href="`#mass-${section.id}`"
          class="mass-guide-toc-link"
        >
          {{ section.title }}
        </a>
      </nav>

      <section
        v-for="section in sections"
        :id="`mass-${section.id}`"
        :key="section.id"
        class="mass-section"
      >
        <h3 class="mass-section-title">{{ section.title }}</h3>

        <div
          v-for="(step, stepIndex) in section.steps"
          :key="`${section.id}-${stepIndex}`"
          class="mass-step"
        >
          <h4 class="mass-step-title">{{ step.title }}</h4>

          <div
            v-for="(part, partIndex) in step.parts"
            :key="`${section.id}-${stepIndex}-${partIndex}`"
            class="mass-part"
            :class="`mass-part--${part.role}`"
          >
            <span class="mass-part-label">{{ part.label }}</span>
            <p class="mass-part-text">{{ part.text }}</p>
          </div>
        </div>
      </section>
    </article>
  </div>
</template>

<script>
import SocialShareBar from '../../components/shared/SocialShareBar.vue';
import { applySeo, buildWebPageJsonLd } from '../../utils/seo';

function todayIsoDate() {
  const now = new Date();
  const offset = now.getTimezoneOffset();
  const local = new Date(now.getTime() - offset * 60 * 1000);
  return local.toISOString().slice(0, 10);
}

function normalizeIsoDate(date) {
  if (!date) return todayIsoDate();
  if (typeof date === 'string') return date.slice(0, 10);
  if (date instanceof Date) return date.toISOString().slice(0, 10);
  return String(date).slice(0, 10);
}

export default {
  name: 'MassGuidePage',
  components: { SocialShareBar },
  data() {
    return {
      mass: null,
      loading: true,
      error: null,
      selectedDate: todayIsoDate(),
    };
  },
  computed: {
    isToday() {
      return this.selectedDate === todayIsoDate();
    },
    sections() {
      return this.mass?.order_of_mass?.sections ?? [];
    },
    shareTitle() {
      const feast = this.mass?.feast_name || 'Daily Mass';
      return this.isToday ? `Today's Mass Guide — ${feast}` : `Mass Guide — ${feast}`;
    },
    shareText() {
      const feast = this.mass?.feast_name || 'Daily Mass';
      const date = this.formatDate(this.mass?.liturgical_date);
      const gospel = this.mass?.gospel_reference ? `Gospel: ${this.mass.gospel_reference}.` : '';
      return `${feast} on ${date}. Full order of Mass with readings and responses. ${gospel}`.trim();
    },
    shareUrl() {
      const path = this.isToday ? '/mass-guide' : `/mass-guide/${this.mass?.liturgical_date}`;
      return `${window.location.origin}${path}`;
    },
  },
  watch: {
    '$route.params.date': {
      immediate: true,
      handler(date) {
        this.selectedDate = date || todayIsoDate();
        this.loadMass();
      },
    },
  },
  methods: {
    async loadMass() {
      this.loading = true;
      this.error = null;

      try {
        const { data } = await window.axios.get('/public/mass/guide', {
          params: { date: this.selectedDate },
        });
        this.mass = {
          ...data,
          liturgical_date: normalizeIsoDate(data.liturgical_date),
          prev_date: data.prev_date ? normalizeIsoDate(data.prev_date) : null,
          next_date: data.next_date ? normalizeIsoDate(data.next_date) : null,
        };
        this.updateSeo();
      } catch {
        this.mass = null;
        this.error = 'No mass guide is available for this date.';
      } finally {
        this.loading = false;
      }
    },
    updateSeo() {
      if (!this.mass) {
        return;
      }

      const feast = this.mass.feast_name || 'Daily Mass';
      const title = this.isToday
        ? `Mass Guide — ${feast}`
        : `Mass Guide — ${feast} (${this.formatDate(this.mass.liturgical_date)})`;

      applySeo({
        title,
        description: this.shareText,
        url: this.shareUrl,
        jsonLd: buildWebPageJsonLd({
          title,
          description: this.shareText,
          url: this.shareUrl,
        }),
      });
    },
    onDateChange() {
      this.navigateToDate(this.selectedDate);
    },
    goToday() {
      this.navigateToDate(todayIsoDate());
    },
    goToDate(date) {
      if (!date) return;
      this.navigateToDate(normalizeIsoDate(date));
    },
    navigateToDate(date) {
      this.selectedDate = normalizeIsoDate(date);
      const routeName = this.selectedDate === todayIsoDate() ? 'mass-guide' : 'mass-guide-date';
      const params = this.selectedDate === todayIsoDate() ? {} : { date: this.selectedDate };

      if (this.$route.name !== routeName || this.$route.params.date !== this.selectedDate) {
        this.$router.push({ name: routeName, params });
      } else {
        this.loadMass();
      }
    },
    formatDate(date) {
      const isoDate = normalizeIsoDate(date);

      return new Date(`${isoDate}T12:00:00`).toLocaleDateString(undefined, {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      });
    },
  },
};
</script>
