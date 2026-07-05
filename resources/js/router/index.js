import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { publicRouteSeo } from '../config/publicSeo';
import { applyRouteSeo } from '../utils/seo';
import PublicLayout from '../layouts/PublicLayout.vue';
import AdminLayout from '../layouts/AdminLayout.vue';

const withSeo = (name, config) => ({
  ...publicRouteSeo[name],
  ...config,
});

const publicChildren = [
  { path: '', name: 'home', component: () => import('../pages/public/HomePage.vue'), meta: withSeo('home', {}) },
  { path: 'bible', name: 'bible', component: () => import('../pages/public/BiblePage.vue'), meta: withSeo('bible', {}) },
  {
    path: 'bible/:version/:book/:chapter',
    name: 'bible-chapter',
    component: () => import('../pages/public/BibleChapterPage.vue'),
    meta: withSeo('bible-chapter', {}),
  },
  { path: 'mass-guide', name: 'mass-guide', component: () => import('../pages/public/MassGuidePage.vue'), meta: withSeo('mass-guide', {}) },
  { path: 'fiesta-calendar', name: 'fiesta-calendar', component: () => import('../pages/public/FiestaCalendarPage.vue'), meta: withSeo('fiesta-calendar', {}) },
  {
    path: 'mass-guide/:date',
    name: 'mass-guide-date',
    component: () => import('../pages/public/MassGuidePage.vue'),
    meta: withSeo('mass-guide', {}),
  },
  { path: 'novenas', name: 'novenas', component: () => import('../pages/public/NovenasPage.vue'), meta: withSeo('novenas', {}) },
  {
    path: 'novenas/:slug',
    name: 'novena-detail',
    component: () => import('../pages/public/NovenaDetailPage.vue'),
    meta: withSeo('novena-detail', {}),
  },
  { path: 'prayers', name: 'prayers', component: () => import('../pages/public/PrayersPage.vue'), meta: withSeo('prayers', {}) },
  {
    path: 'prayers/:slug',
    name: 'prayer-detail',
    component: () => import('../pages/public/PrayerDetailPage.vue'),
    meta: withSeo('prayer-detail', {}),
  },
  { path: 'ai-advice', name: 'ai-advice', component: () => import('../pages/public/AiAdvicePage.vue'), meta: withSeo('ai-advice', {}) },
  {
    path: 'about',
    name: 'about',
    component: () => import('../pages/public/StaticPageView.vue'),
    meta: withSeo('about', { slug: 'about-us' }),
  },
  {
    path: 'privacy',
    name: 'privacy',
    component: () => import('../pages/public/StaticPageView.vue'),
    meta: withSeo('privacy', { slug: 'privacy-policy' }),
  },
  {
    path: 'terms',
    name: 'terms',
    component: () => import('../pages/public/StaticPageView.vue'),
    meta: withSeo('terms', { slug: 'terms-and-conditions' }),
  },
  { path: 'contact', name: 'contact', component: () => import('../pages/public/ContactPage.vue'), meta: withSeo('contact', {}) },
  { path: 'donate', name: 'donate', component: () => import('../pages/public/DonatePage.vue'), meta: withSeo('donate', {}) },
];

const adminListPageLoaders = {
  'bible-versions': () => import('../pages/admin/lists/BibleVersionsListPage.vue'),
  'bible-books': () => import('../pages/admin/lists/BibleBooksListPage.vue'),
  'bible-chapters': () => import('../pages/admin/lists/BibleChaptersListPage.vue'),
  'bible-verses': () => import('../pages/admin/lists/BibleVersesListPage.vue'),
  'mass-guides': () => import('../pages/admin/lists/MassGuidesListPage.vue'),
  fiestas: () => import('../pages/admin/lists/FiestasListPage.vue'),
  novenas: () => import('../pages/admin/lists/NovenasListPage.vue'),
  prayers: () => import('../pages/admin/lists/PrayersListPage.vue'),
  proverbs: () => import('../pages/admin/lists/ProverbsListPage.vue'),
  'daily-psalms': () => import('../pages/admin/lists/DailyPsalmsListPage.vue'),
  advertisements: () => import('../pages/admin/lists/AdvertisementsListPage.vue'),
  'ad-invoices': () => import('../pages/admin/lists/AdInvoicesListPage.vue'),
  'social-media': () => import('../pages/admin/lists/SocialMediaListPage.vue'),
  'system-settings': () => import('../pages/admin/lists/SystemSettingsListPage.vue'),
  'page-banners': () => import('../pages/admin/lists/PageBannersListPage.vue'),
  'donation-settings': () => import('../pages/admin/lists/DonationSettingsListPage.vue'),
  'static-pages': () => import('../pages/admin/lists/StaticPagesListPage.vue'),
  'contact-messages': () => import('../pages/admin/lists/ContactMessagesListPage.vue'),
  'ai-conversations': () => import('../pages/admin/lists/AiConversationsListPage.vue'),
  'page-visits': () => import('../pages/admin/lists/PageVisitsListPage.vue'),
};

const adminResourceRoutes = Object.keys(adminListPageLoaders).map((resourceKey) => ({
  path: resourceKey,
  name: `admin-${resourceKey}`,
  component: adminListPageLoaders[resourceKey],
  meta: { resourceKey, requiresAuth: true, requiresSuperAdmin: true },
}));

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/install',
      name: 'install',
      component: () => import('../pages/install/InstallWizardPage.vue'),
      meta: { installOnly: true },
    },
    {
      path: '/',
      component: PublicLayout,
      children: publicChildren,
    },
    {
      path: '/admin/login',
      name: 'admin-login',
      component: () => import('../pages/admin/LoginPage.vue'),
      meta: { guestOnly: true },
    },
    {
      path: '/admin',
      component: AdminLayout,
      meta: { requiresAuth: true, requiresSuperAdmin: true },
      children: [
        {
          path: '',
          redirect: { name: 'admin-dashboard' },
        },
        {
          path: 'dashboard',
          name: 'admin-dashboard',
          component: () => import('../pages/admin/DashboardPage.vue'),
          meta: { requiresAuth: true, requiresSuperAdmin: true, title: 'Dashboard' },
        },
        ...adminResourceRoutes,
      ],
    },
    {
      path: '/:pathMatch(.*)*',
      redirect: { name: 'home' },
    },
  ],
  scrollBehavior() {
    return { top: 0 };
  },
});

router.beforeEach(async (to) => {
  if (to.meta.installOnly) {
    applyRouteSeo(to);
    return true;
  }

  const auth = useAuthStore();

  if (!auth.initialized) {
    await auth.initialize();
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'admin-login', query: { redirect: to.fullPath } };
  }

  if (to.meta.requiresSuperAdmin && auth.isAuthenticated && !auth.isSuperAdmin) {
    return { name: 'home' };
  }

  if (to.meta.guestOnly && auth.isAuthenticated) {
    return { name: 'admin-dashboard' };
  }

  applyRouteSeo(to);

  return true;
});

export default router;
