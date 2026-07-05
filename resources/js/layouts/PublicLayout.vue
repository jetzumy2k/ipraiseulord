<template>
  <div class="public-site" :class="liturgicalThemeClass">
    <header class="site-header">
      <div class="header-top">
        <div class="container d-flex justify-content-between align-items-center py-2">
          <div class="header-meta">
            <span v-if="showDate">{{ formattedDate }}</span>
            <span v-if="showDate && showTemperature && temperature" class="mx-2">|</span>
            <span v-if="showTemperature && temperature">{{ temperature }}</span>
          </div>
          <router-link to="/admin/login" class="admin-link">Admin</router-link>
        </div>
      </div>
      <div
        class="site-banner"
        :class="{ 'site-banner--image': hasBannerImage }"
        :style="bannerStyle"
      >
        <div
          v-if="hasBannerImage"
          class="site-banner__overlay"
          :style="bannerOverlayStyle"
        />
        <div class="site-banner__content container text-center py-4">
          <router-link to="/" class="site-logo">{{ siteName }}</router-link>
          <p v-if="bannerVerse" class="daily-psalm mt-3 mb-0">
            <em>{{ bannerVerse.reference }}</em>
            <span v-if="bannerVerse.version" class="banner-verse-version">({{ bannerVerse.version }})</span>
            <span class="d-block psalm-text">{{ bannerVerse.text }}</span>
          </p>
        </div>
      </div>
      <nav class="site-nav">
        <div class="container">
          <ul class="nav justify-content-center flex-wrap">
            <li v-for="item in filteredNavItems" :key="item.to" class="nav-item">
              <router-link :to="item.to" class="nav-link" active-class="active">
                {{ item.label }}
              </router-link>
            </li>
          </ul>
        </div>
      </nav>
    </header>

    <main class="site-main">
      <div class="container py-4">
        <div class="row g-4">
          <aside class="col-lg-3 order-lg-1">
            <div class="sidebar-card">
              <h3 class="sidebar-title">Daily Proverb</h3>
              <blockquote v-if="proverb" class="proverb-quote">
                <p>{{ proverb.text }}</p>
                <footer>{{ proverb.reference }}</footer>
              </blockquote>
            </div>
            <SidebarVerseWidget title="Psalm" scope="psalms" />
            <div v-for="ad in sidebarAds" :key="ad.id" class="sidebar-card ad-card" v-html="renderAd(ad)" />
            <DonationWidget
              v-if="showDonations && $route.name !== 'donate'"
              :donations="donations"
              location="footer"
              compact
            />
          </aside>

          <section class="col-lg-6 order-lg-2">
            <router-view />
          </section>

          <aside class="col-lg-3 order-lg-3">
            <SidebarVerseWidget title="Old Testament" scope="old-testament" />
            <SidebarVerseWidget title="New Testament" scope="new-testament" />
            <div v-for="ad in rightAds" :key="ad.id" class="sidebar-card ad-card" v-html="renderAd(ad)" />
          </aside>
        </div>
      </div>
    </main>

    <footer class="site-footer">
      <div class="container py-4">
        <div v-if="footerAds.length" class="footer-ads row g-3 mb-4">
          <div v-for="ad in footerAds" :key="ad.id" class="col-md-4" v-html="renderAd(ad)" />
        </div>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
          <p class="mb-0">&copy; {{ year }} {{ siteName }}. All rights reserved.</p>
          <div class="social-links">
            <a
              v-for="social in socialLinks"
              :key="social.id"
              :href="social.url"
              target="_blank"
              rel="noopener"
              class="social-link me-3"
            >
              <i :class="socialIcon(social.platform)" /> {{ social.platform }}
            </a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import DonationWidget from '../components/shared/DonationWidget.vue';
import SidebarVerseWidget from '../components/shared/SidebarVerseWidget.vue';
import { useVisitor } from '../composables/useVisitor';
import { initSeoDefaults, applyRouteSeo } from '../utils/seo';
import { resolveBannerWithFallback } from '../config/banner';
import { liturgicalThemeClass as getLiturgicalThemeClass } from '../utils/liturgicalColors';

const BANNER_VERSE_INTERVAL_MS = 5 * 60 * 1000;

export default {
  name: 'PublicLayout',
  components: { DonationWidget, SidebarVerseWidget },
  data() {
    return {
      settings: null,
      banners: {},
      bannerVerse: null,
      verseIntervalId: null,
      proverb: null,
      sidebarAds: [],
      rightAds: [],
      footerAds: [],
      donations: [],
      donationsEnabled: false,
      socialLinks: [],
      liturgicalColor: null,
      navItems: [
        { to: '/', label: 'Home' },
        { to: '/bible', label: 'Bible' },
        { to: '/mass-guide', label: 'Mass Guide' },
        { to: '/fiesta-calendar', label: 'Fiesta Calendar' },
        { to: '/novenas', label: 'Novenas' },
        { to: '/prayers', label: 'Prayers' },
        { to: '/ai-advice', label: 'AI Advice' },
        { to: '/about', label: 'About' },
        { to: '/contact', label: 'Contact' },
        { to: '/donate', label: 'Donate' },
      ],
    };
  },
  computed: {
    siteName() {
      return this.getSetting('site_name', 'Praise U Lord');
    },
    showDate() {
      return this.getSetting('show_date', 'true') === 'true';
    },
    showTemperature() {
      return this.getSetting('show_temperature', 'true') === 'true';
    },
    temperature() {
      const live = this.settings?.weather?.temperature;
      if (live) {
        return live;
      }

      const manual = this.getSetting('temperature', '');
      if (manual && !manual.includes('—')) {
        return manual;
      }

      return null;
    },
    formattedDate() {
      return new Date().toLocaleDateString(undefined, {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      });
    },
    year() {
      return new Date().getFullYear();
    },
    liturgicalThemeClass() {
      return getLiturgicalThemeClass(this.liturgicalColor);
    },
    showDonations() {
      return this.donationsEnabled && this.donations.length > 0;
    },
    filteredNavItems() {
      return this.navItems.filter((item) => item.to !== '/donate' || this.showDonations);
    },
    currentBanner() {
      return resolveBannerWithFallback(this.banners, this.$route.name);
    },
    hasBannerImage() {
      return Boolean(this.currentBanner?.image_path);
    },
    bannerStyle() {
      if (!this.hasBannerImage) {
        return null;
      }

      return {
        backgroundImage: `url(${this.currentBanner.image_path})`,
      };
    },
    bannerOverlayStyle() {
      const opacity = this.currentBanner?.overlay_opacity ?? 0.55;

      return {
        opacity,
      };
    },
  },
  watch: {
    '$route'() {
      this.trackVisit();
    },
  },
  mounted() {
    this.loadPublicData();
    this.trackVisit();
    this.loadBannerVerse();
    this.verseIntervalId = window.setInterval(() => {
      this.loadBannerVerse();
    }, BANNER_VERSE_INTERVAL_MS);
  },
  beforeUnmount() {
    if (this.verseIntervalId) {
      window.clearInterval(this.verseIntervalId);
    }
  },
  methods: {
    getSetting(key, fallback = '') {
      const groups = this.settings?.system || {};
      for (const group of Object.values(groups)) {
        const found = group.find((s) => s.key === key);
        if (found) return found.value;
      }
      return fallback;
    },
    async loadPublicData() {
      try {
        const [settings, banners, proverb, sidebarAds, rightAds, footerAds, massToday] = await Promise.all([
          window.axios.get('/public/settings').then((r) => r.data).catch(() => null),
          window.axios.get('/public/banners').then((r) => r.data).catch(() => ({})),
          window.axios.get('/public/proverb/random').then((r) => r.data).catch(() => null),
          window.axios.get('/public/ads/sidebar-left').then((r) => r.data).catch(() => []),
          window.axios.get('/public/ads/sidebar-right').then((r) => r.data).catch(() => []),
          window.axios.get('/public/ads/footer').then((r) => r.data).catch(() => []),
          window.axios.get('/public/mass/today').then((r) => r.data).catch(() => null),
        ]);
        this.settings = settings;
        if (settings?.seo) {
          initSeoDefaults(settings.seo);
          applyRouteSeo(this.$route);
        }
        this.banners = banners || {};
        this.proverb = proverb;
        this.sidebarAds = sidebarAds;
        this.rightAds = rightAds;
        this.footerAds = footerAds;
        this.liturgicalColor = massToday?.liturgical_color || null;
        this.donationsEnabled = settings?.donations_enabled !== false;
        this.donations = this.donationsEnabled ? (settings?.donations || []) : [];
        this.socialLinks = settings?.social_media || [];
      } catch {
        // public widgets are optional
      }
    },
    async loadBannerVerse() {
      try {
        const { data } = await window.axios.get('/public/bible/verse/random/any');
        this.bannerVerse = data;
      } catch {
        try {
          const { data } = await window.axios.get('/public/psalm/random');
          this.bannerVerse = {
            reference: data.reference || data.title,
            text: data.text,
          };
        } catch {
          this.bannerVerse = null;
        }
      }
    },
    renderAd(ad) {
      if (ad.type === 'embed' && ad.embed_script) return ad.embed_script;
      if (ad.image_path) {
        const target = ad.url ? `<a href="${ad.url}" target="_blank" rel="noopener">` : '';
        const end = ad.url ? '</a>' : '';
        return `${target}<img src="${ad.image_path}" alt="${ad.name}" class="img-fluid">${end}`;
      }
      return ad.url ? `<a href="${ad.url}" target="_blank" rel="noopener">${ad.name}</a>` : ad.name;
    },
    socialIcon(platform) {
      const map = {
        facebook: 'fab fa-facebook',
        twitter: 'fab fa-x-twitter',
        instagram: 'fab fa-instagram',
        youtube: 'fab fa-youtube',
      };
      return map[platform?.toLowerCase()] || 'fas fa-link';
    },
    trackVisit() {
      const { visitorId } = useVisitor();
      window.axios.post('/public/visits', {
        visitor_id: visitorId,
        page_type: this.$route.name || 'page',
        page_slug: this.$route.path,
        page_title: this.$route.meta.title || document.title,
      }).catch(() => {});
    },
  },
};
</script>
