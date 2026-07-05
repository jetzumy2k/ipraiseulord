export const BANNER_WIDTH = 1920;
export const BANNER_HEIGHT = 480;
export const BANNER_ASPECT_RATIO = BANNER_WIDTH / BANNER_HEIGHT;

export const BANNER_ROUTE_FALLBACKS = {
  'bible-chapter': 'bible',
  'mass-guide-date': 'mass-guide',
  'novena-detail': 'novenas',
  'prayer-detail': 'prayers',
};

export function resolveBannerRouteName(routeName) {
  if (!routeName) {
    return 'home';
  }

  return routeName;
}

export function resolveBannerWithFallback(banners, routeName) {
  if (!banners || !routeName) {
    return null;
  }

  const direct = banners[routeName];

  if (direct?.image_path) {
    return direct;
  }

  const fallbackKey = BANNER_ROUTE_FALLBACKS[routeName];
  const fallback = fallbackKey ? banners[fallbackKey] : null;

  if (fallback?.image_path) {
    return fallback;
  }

  const home = banners.home;

  if (home?.image_path) {
    return home;
  }

  return direct || fallback || home || null;
}
