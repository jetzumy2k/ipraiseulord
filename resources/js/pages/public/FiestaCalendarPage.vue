<template>
  <div class="public-page fiesta-page">
    <PageStaticHeader
      route-name="fiesta-calendar"
      default-title="Fiesta Calendar"
      default-intro="Feast days of Jesus Christ, the Holy Trinity, Mary, the saints, and the angels."
      title-class="mb-1"
      intro-class="text-muted mb-0"
      compact
      update-seo
    />

    <div class="d-flex flex-wrap align-items-center justify-content-end gap-3 mb-3">
      <div class="fiesta-nav">
        <button type="button" class="btn btn-sm btn-outline-primary" @click="prevMonth">
          <i class="fas fa-chevron-left" />
        </button>
        <select v-model.number="month" class="form-select form-select-sm fiesta-month-select" @change="loadEvents">
          <option v-for="item in monthOptions" :key="item.value" :value="item.value">
            {{ item.label }}
          </option>
        </select>
        <select v-model.number="year" class="form-select form-select-sm fiesta-year-select" @change="loadEvents">
          <option v-for="item in yearOptions" :key="item" :value="item">{{ item }}</option>
        </select>
        <button type="button" class="btn btn-sm btn-outline-primary" @click="nextMonth">
          <i class="fas fa-chevron-right" />
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" :disabled="isCurrentMonth" @click="goToday">
          Today
        </button>
      </div>
    </div>

    <div class="fiesta-filters content-card mb-4">
      <span class="fiesta-filters-label">Show:</span>
      <button
        type="button"
        class="btn btn-sm"
        :class="category === '' ? 'btn-primary' : 'btn-outline-primary'"
        @click="setCategory('')"
      >
        All
      </button>
      <button
        v-for="item in categories"
        :key="item.value"
        type="button"
        class="btn btn-sm"
        :class="category === item.value ? 'btn-primary' : 'btn-outline-primary'"
        @click="setCategory(item.value)"
      >
        {{ item.label }}
      </button>
    </div>

    <div v-if="loading" class="text-center py-5">
      <i class="fas fa-spinner fa-spin fa-2x" />
    </div>

    <template v-else>
      <div class="content-card fiesta-calendar mb-4">
        <div class="fiesta-calendar-head">
          <div v-for="weekday in weekdays" :key="weekday" class="fiesta-calendar-weekday">
            {{ weekday }}
          </div>
        </div>
        <div class="fiesta-calendar-grid">
          <button
            v-for="cell in calendarCells"
            :key="cell.key"
            type="button"
            class="fiesta-calendar-cell"
            :class="{
              'fiesta-calendar-cell--muted': !cell.inMonth,
              'fiesta-calendar-cell--today': cell.isToday,
              'fiesta-calendar-cell--selected': selectedDay === cell.day && cell.inMonth,
              'fiesta-calendar-cell--has-events': cell.events.length,
            }"
            :disabled="!cell.inMonth"
            @click="selectDay(cell)"
          >
            <span class="fiesta-calendar-day">{{ cell.day }}</span>
            <span v-if="cell.events.length" class="fiesta-calendar-dots">
              <span
                v-for="event in cell.events.slice(0, 3)"
                :key="event.id"
                class="fiesta-calendar-dot"
                :class="`fiesta-calendar-dot--${event.category}`"
              />
            </span>
          </button>
        </div>
      </div>

      <section class="content-card fiesta-events-panel">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
          <h2 class="section-title mb-0">{{ selectedDayLabel }}</h2>
          <SocialShareBar
            v-if="selectedEvents.length"
            :title="shareTitle"
            :text="shareText"
            :url="shareUrl"
            compact
          />
        </div>
        <p v-if="!selectedEvents.length" class="text-muted mb-0">No fiestas scheduled for this day.</p>
        <ul v-else class="fiesta-events-list">
          <li v-for="event in selectedEvents" :key="`${event.id}-${event.date}`" class="fiesta-event">
            <span class="fiesta-event-badge" :class="`fiesta-event-badge--${event.category}`">
              {{ categoryLabel(event.category) }}
            </span>
            <div>
              <h3 class="fiesta-event-title">{{ event.title }}</h3>
              <p v-if="event.honoree_name" class="fiesta-event-honoree mb-1">{{ event.honoree_name }}</p>
              <p v-if="event.liturgical_rank" class="fiesta-event-rank text-muted mb-1">
                {{ formatRank(event.liturgical_rank) }}
                <span v-if="event.is_movable"> · Movable feast</span>
              </p>
              <p v-if="event.description" class="fiesta-event-description mb-0">{{ event.description }}</p>
            </div>
          </li>
        </ul>
      </section>
    </template>
  </div>
</template>

<script>
import PageStaticHeader from '../../components/shared/PageStaticHeader.vue';
import SocialShareBar from '../../components/shared/SocialShareBar.vue';

const monthNames = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December',
];

export default {
  name: 'FiestaCalendarPage',
  components: { PageStaticHeader, SocialShareBar },
  data() {
    const today = new Date();

    return {
      loading: true,
      year: today.getFullYear(),
      month: today.getMonth() + 1,
      category: '',
      events: [],
      selectedDay: today.getDate(),
      weekdays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
      categories: [
        { value: 'christ', label: 'Jesus Christ' },
        { value: 'father', label: 'Holy Trinity' },
        { value: 'mary', label: 'Mary' },
        { value: 'saint', label: 'Saints' },
        { value: 'angel', label: 'Angels' },
      ],
    };
  },
  computed: {
    monthOptions() {
      return monthNames.map((label, index) => ({ value: index + 1, label }));
    },
    yearOptions() {
      const current = new Date().getFullYear();
      return Array.from({ length: 11 }, (_, index) => current - 5 + index);
    },
    isCurrentMonth() {
      const today = new Date();
      return this.year === today.getFullYear() && this.month === today.getMonth() + 1;
    },
    eventsByDay() {
      return this.events.reduce((map, event) => {
        if (!map[event.day]) {
          map[event.day] = [];
        }
        map[event.day].push(event);
        return map;
      }, {});
    },
    calendarCells() {
      const firstDay = new Date(this.year, this.month - 1, 1);
      const startOffset = firstDay.getDay();
      const daysInMonth = new Date(this.year, this.month, 0).getDate();
      const daysInPrevMonth = new Date(this.year, this.month - 1, 0).getDate();
      const today = new Date();
      const cells = [];

      for (let index = 0; index < 42; index += 1) {
        const dayNumber = index - startOffset + 1;
        let day = dayNumber;
        let inMonth = true;

        if (dayNumber < 1) {
          day = daysInPrevMonth + dayNumber;
          inMonth = false;
        } else if (dayNumber > daysInMonth) {
          day = dayNumber - daysInMonth;
          inMonth = false;
        }

        cells.push({
          key: `${this.year}-${this.month}-${index}`,
          day,
          inMonth,
          isToday: inMonth
            && day === today.getDate()
            && this.month === today.getMonth() + 1
            && this.year === today.getFullYear(),
          events: inMonth ? (this.eventsByDay[day] || []) : [],
        });
      }

      return cells;
    },
    selectedEvents() {
      return this.eventsByDay[this.selectedDay] || [];
    },
    selectedDayLabel() {
      const date = new Date(this.year, this.month - 1, this.selectedDay);
      return date.toLocaleDateString(undefined, {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
        year: 'numeric',
      });
    },
    shareTitle() {
      if (!this.selectedEvents.length) {
        return '';
      }

      if (this.selectedEvents.length === 1) {
        return `${this.selectedEvents[0].title} — Fiesta Calendar`;
      }

      return `Fiestas on ${this.selectedDayLabel}`;
    },
    shareText() {
      if (!this.selectedEvents.length) {
        return '';
      }

      const eventLines = this.selectedEvents.map((event) => {
        let line = event.title;

        if (event.honoree_name && event.honoree_name !== event.title) {
          line += ` (${event.honoree_name})`;
        }

        if (event.liturgical_rank) {
          line += ` · ${this.formatRank(event.liturgical_rank)}`;
        }

        if (event.description) {
          line += ` — ${event.description}`;
        }

        return line;
      });

      return `${eventLines.join('\n\n')}\n\n${this.selectedDayLabel}`;
    },
    shareUrl() {
      const params = new URLSearchParams({
        year: String(this.year),
        month: String(this.month),
        day: String(this.selectedDay),
      });

      return `${window.location.origin}/fiesta-calendar?${params.toString()}`;
    },
  },
  mounted() {
    this.applyQuerySelection();
    this.loadEvents();
  },
  methods: {
    applyQuerySelection() {
      const params = new URLSearchParams(window.location.search);
      const year = Number(params.get('year'));
      const month = Number(params.get('month'));
      const day = Number(params.get('day'));

      if (year >= 1900 && year <= 2100) {
        this.year = year;
      }

      if (month >= 1 && month <= 12) {
        this.month = month;
      }

      if (day >= 1 && day <= 31) {
        this.selectedDay = day;
      }
    },
    async loadEvents() {
      this.loading = true;

      try {
        const params = { year: this.year, month: this.month };
        if (this.category) {
          params.category = this.category;
        }

        const { data } = await window.axios.get('/public/fiestas/calendar', { params });
        this.events = data.events || [];

        const maxDay = new Date(this.year, this.month, 0).getDate();
        if (this.selectedDay > maxDay) {
          this.selectedDay = maxDay;
        }
      } finally {
        this.loading = false;
      }
    },
    setCategory(value) {
      this.category = value;
      this.loadEvents();
    },
    selectDay(cell) {
      if (!cell.inMonth) {
        return;
      }

      this.selectedDay = cell.day;
    },
    prevMonth() {
      if (this.month === 1) {
        this.month = 12;
        this.year -= 1;
      } else {
        this.month -= 1;
      }
      this.loadEvents();
    },
    nextMonth() {
      if (this.month === 12) {
        this.month = 1;
        this.year += 1;
      } else {
        this.month += 1;
      }
      this.loadEvents();
    },
    goToday() {
      const today = new Date();
      this.year = today.getFullYear();
      this.month = today.getMonth() + 1;
      this.selectedDay = today.getDate();
      this.loadEvents();
    },
    categoryLabel(value) {
      return this.categories.find((item) => item.value === value)?.label || value;
    },
    formatRank(value) {
      return String(value).replace(/_/g, ' ');
    },
  },
};
</script>
