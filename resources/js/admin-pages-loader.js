let adminPagesPromise = null;

export function prefetchAdminPages() {
  if (!adminPagesPromise) {
    adminPagesPromise = import('./admin-pages.js');
  }

  return adminPagesPromise;
}

export function loadAdminPage(exportName) {
  return prefetchAdminPages().then((module) => module[exportName]);
}
