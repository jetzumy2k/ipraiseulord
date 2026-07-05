<template>
  <div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="#" role="button" @click.prevent="toggleSidebar">
            <i class="fas fa-bars" />
          </a>
        </li>
      </ul>
      <ul class="navbar-nav ml-auto">
        <li class="nav-item d-none d-sm-inline-block">
          <router-link to="/" class="nav-link">View Site</router-link>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" @click.prevent="logout">
            <i class="fas fa-sign-out-alt" /> Logout
          </a>
        </li>
      </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <router-link to="/admin/dashboard" class="brand-link">
        <span class="brand-text font-weight-light ms-2">Praise U Lord Admin</span>
      </router-link>
      <div class="sidebar">
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
            <li class="nav-item">
              <router-link to="/admin/dashboard" class="nav-link" active-class="active">
                <i class="nav-icon fas fa-tachometer-alt" />
                <p>Dashboard</p>
              </router-link>
            </li>

            <template v-for="group in menuGroups" :key="group.label">
              <li class="nav-header">{{ group.label }}</li>
              <li v-for="item in group.items" :key="item.to" class="nav-item">
                <router-link :to="item.to" class="nav-link" active-class="active">
                  <i class="nav-icon" :class="item.icon" />
                  <p>{{ item.label }}</p>
                </router-link>
              </li>
            </template>
          </ul>
        </nav>
      </div>
    </aside>

    <div class="content-wrapper">
      <section class="content pt-3">
        <div class="container-fluid">
          <router-view />
        </div>
      </section>
    </div>

    <footer class="main-footer">
      <strong>Praise U Lord</strong> Admin Panel
    </footer>
  </div>
</template>

<script>
import { useAuthStore } from '../stores/auth';

export default {
  name: 'AdminLayout',
  data() {
    return {
      menuGroups: [
        {
          label: 'Bible',
          items: [
            { to: '/admin/bible-versions', label: 'Versions', icon: 'fas fa-book-bible' },
            { to: '/admin/bible-books', label: 'Books', icon: 'fas fa-book' },
            { to: '/admin/bible-chapters', label: 'Chapters', icon: 'fas fa-bookmark' },
            { to: '/admin/bible-verses', label: 'Verses', icon: 'fas fa-paragraph' },
          ],
        },
        {
          label: 'Content',
          items: [
            { to: '/admin/mass-guides', label: 'Mass Guides', icon: 'fas fa-church' },
            { to: '/admin/fiestas', label: 'Fiesta Calendar', icon: 'fas fa-calendar-alt' },
            { to: '/admin/novenas', label: 'Novenas', icon: 'fas fa-praying-hands' },
            { to: '/admin/prayers', label: 'Prayers', icon: 'fas fa-hands' },
            { to: '/admin/proverbs', label: 'Proverbs', icon: 'fas fa-quote-left' },
            { to: '/admin/daily-psalms', label: 'Daily Psalms', icon: 'fas fa-sun' },
            { to: '/admin/static-pages', label: 'Static Pages', icon: 'fas fa-file-alt' },
          ],
        },
        {
          label: 'Advertising',
          items: [
            { to: '/admin/advertisements', label: 'Advertisements', icon: 'fas fa-ad' },
            { to: '/admin/ad-invoices', label: 'Ad Invoices', icon: 'fas fa-file-invoice-dollar' },
          ],
        },
        {
          label: 'Settings',
          items: [
            { to: '/admin/system-settings', label: 'System Settings', icon: 'fas fa-cog' },
            { to: '/admin/page-banners', label: 'Page Banners', icon: 'fas fa-image' },
            { to: '/admin/donation-settings', label: 'Donation Settings', icon: 'fas fa-hand-holding-heart' },
            { to: '/admin/social-media', label: 'Social Media', icon: 'fas fa-share-alt' },
          ],
        },
        {
          label: 'Communications',
          items: [
            { to: '/admin/contact-messages', label: 'Contact Messages', icon: 'fas fa-envelope' },
            { to: '/admin/ai-conversations', label: 'AI Conversations', icon: 'fas fa-robot' },
          ],
        },
        {
          label: 'Analytics',
          items: [
            { to: '/admin/page-visits', label: 'Page Visits', icon: 'fas fa-chart-line' },
          ],
        },
      ],
    };
  },
  mounted() {
    document.body.classList.add('sidebar-mini', 'layout-fixed');
  },
  unmounted() {
    document.body.classList.remove('sidebar-mini', 'layout-fixed');
  },
  methods: {
    toggleSidebar() {
      document.body.classList.toggle('sidebar-collapse');
    },
    async logout() {
      const auth = useAuthStore();
      await auth.logout();
      this.$router.push({ name: 'admin-login' });
    },
  },
};
</script>
