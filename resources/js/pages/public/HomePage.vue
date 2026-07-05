<template>
  <div class="public-page home-page">
    <h1 class="page-title">Welcome</h1>
    <p class="lead">
      A place for daily scripture, prayer, and Catholic devotion — inspired by the beauty of sacred reading.
    </p>

    <div class="row g-3 mt-2">
      <div v-for="card in featureCards" :key="card.to" class="col-md-6">
        <router-link :to="card.to" class="feature-card">
          <i :class="card.icon" />
          <h3>{{ card.title }}</h3>
          <p>{{ card.description }}</p>
        </router-link>
      </div>
    </div>

    <section v-if="massGuide" class="mt-5">
      <h2 class="section-title">Today's Mass</h2>
      <div class="content-card">
        <p class="mb-1"><strong>{{ massGuide.feast_name || 'Daily Mass' }}</strong></p>
        <p v-if="massGuide.gospel_reference" class="text-muted mb-2">{{ massGuide.gospel_reference }}</p>
        <p class="mb-0">{{ excerpt(massGuide.gospel_text) }}</p>
        <router-link to="/mass-guide" class="read-more">Read full mass guide &rarr;</router-link>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'HomePage',
  data() {
    return {
      massGuide: null,
      featureCards: [
        { to: '/bible', title: 'Holy Bible', description: 'Read scripture in multiple translations.', icon: 'fas fa-book-bible' },
        { to: '/mass-guide', title: 'Mass Guide', description: "Today's readings and liturgy.", icon: 'fas fa-church' },
        { to: '/novenas', title: 'Novenas', description: 'Nine-day prayers of devotion.', icon: 'fas fa-praying-hands' },
        { to: '/prayers', title: 'Prayers', description: 'Classic and daily prayers.', icon: 'fas fa-hands' },
        { to: '/ai-advice', title: 'AI Advice', description: 'Scripture-based spiritual guidance.', icon: 'fas fa-robot' },
        { to: '/donate', title: 'Donate', description: 'Support this ministry.', icon: 'fas fa-hand-holding-heart' },
      ],
    };
  },
  mounted() {
    window.axios.get('/public/mass/today').then(({ data }) => {
      this.massGuide = data;
    }).catch(() => {});
  },
  methods: {
    excerpt(text) {
      if (!text) return '';
      return text.length > 200 ? `${text.slice(0, 200)}…` : text;
    },
  },
};
</script>
