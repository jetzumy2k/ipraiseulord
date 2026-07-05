const JSON_LD_SITE_ID = 'seo-jsonld-site';
const JSON_LD_PAGE_ID = 'seo-jsonld-page';

let defaults = {
  site_name: 'Praise U Lord',
  description: 'Read the Bible, follow daily Mass guides, novenas, prayers, and Catholic feast days.',
  keywords: 'bible, catholic, mass guide, novenas, prayers',
  site_url: typeof window !== 'undefined' ? window.location.origin : '',
  og_image: null,
  twitter_site: null,
  locale: 'en',
};

export function initSeoDefaults(config = {}) {
  defaults = {
    ...defaults,
    ...config,
    site_url: config.site_url || defaults.site_url || (typeof window !== 'undefined' ? window.location.origin : ''),
  };

  applySiteJsonLd();
}

export function applySeo(options = {}) {
  const siteName = options.siteName || defaults.site_name || 'Praise U Lord';
  const pageTitle = options.title || siteName;
  const fullTitle = options.rawTitle
    || (pageTitle === siteName ? siteName : `${pageTitle} | ${siteName}`);
  const description = truncate(stripHtml(options.description || defaults.description || ''), 160);
  const url = absoluteUrl(options.url || options.canonical || (typeof window !== 'undefined' ? window.location.href : ''));
  const canonical = absoluteUrl(options.canonical || url);
  const image = absoluteUrl(options.image || defaults.og_image);
  const type = options.type || 'website';
  const robots = options.robots || 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
  const keywords = options.keywords || defaults.keywords;

  if (typeof document !== 'undefined') {
    document.title = fullTitle;

    setMeta('description', description);
    setMeta('keywords', keywords);
    setMeta('robots', robots);
    setMeta('author', siteName);
    setMeta('application-name', siteName);

    setLink('canonical', canonical);

    setMeta('og:title', fullTitle, 'property');
    setMeta('og:description', description, 'property');
    setMeta('og:url', canonical, 'property');
    setMeta('og:type', type, 'property');
    setMeta('og:site_name', siteName, 'property');
    setMeta('og:locale', defaults.locale || 'en', 'property');
    setMeta('og:image', image, 'property');
    setMeta('og:image:alt', pageTitle, 'property');

    setMeta('twitter:card', image ? 'summary_large_image' : 'summary');
    setMeta('twitter:title', fullTitle);
    setMeta('twitter:description', description);
    setMeta('twitter:image', image);
    setMeta('twitter:site', defaults.twitter_site || null);

    if (options.jsonLd) {
      setJsonLd(JSON_LD_PAGE_ID, options.jsonLd);
    } else if (options.clearPageJsonLd !== false) {
      removeJsonLd(JSON_LD_PAGE_ID);
    }
  }

  return {
    title: fullTitle,
    description,
    url: canonical,
    image,
  };
}

export function applyRouteSeo(route, extra = {}) {
  const meta = route.meta || {};
  const isAdmin = route.path.startsWith('/admin');
  const siteName = defaults.site_name || 'Praise U Lord';
  let title = meta.title || siteName;

  if (route.name === 'home') {
    title = 'Home';
  }

  const seoOptions = {
    title,
    description: meta.description || defaults.description,
    type: meta.ogType || 'website',
    robots: isAdmin ? 'noindex, nofollow' : (meta.robots || undefined),
    url: typeof window !== 'undefined' ? `${window.location.origin}${route.fullPath.split('#')[0]}` : undefined,
    jsonLd: meta.jsonLd,
    ...extra,
  };

  if (route.name === 'home') {
    seoOptions.rawTitle = `${siteName} — Mass Guide, Novenas, Prayers & Bible`;
  }

  return applySeo(seoOptions);
}

export function buildWebPageJsonLd({ title, description, url, breadcrumbs = [] }) {
  const payload = {
    '@context': 'https://schema.org',
    '@type': 'WebPage',
    name: title,
    description,
    url,
    isPartOf: {
      '@type': 'WebSite',
      name: defaults.site_name,
      url: defaults.site_url,
    },
  };

  if (breadcrumbs.length) {
    payload.breadcrumb = {
      '@type': 'BreadcrumbList',
      itemListElement: breadcrumbs.map((item, index) => ({
        '@type': 'ListItem',
        position: index + 1,
        name: item.name,
        item: absoluteUrl(item.item),
      })),
    };
  }

  return payload;
}

export function buildArticleJsonLd({ title, description, url, author, datePublished, dateModified }) {
  return {
    '@context': 'https://schema.org',
    '@type': 'Article',
    headline: title,
    description,
    url,
    author: {
      '@type': 'Organization',
      name: author || defaults.site_name,
    },
    publisher: {
      '@type': 'Organization',
      name: defaults.site_name,
      url: defaults.site_url,
    },
    datePublished: datePublished || undefined,
    dateModified: dateModified || datePublished || undefined,
    mainEntityOfPage: url,
  };
}

function applySiteJsonLd() {
  if (typeof document === 'undefined' || !defaults.site_url) {
    return;
  }

  setJsonLd(JSON_LD_SITE_ID, [
    {
      '@context': 'https://schema.org',
      '@type': 'Organization',
      name: defaults.site_name,
      url: defaults.site_url,
      description: defaults.description,
    },
    {
      '@context': 'https://schema.org',
      '@type': 'WebSite',
      name: defaults.site_name,
      url: defaults.site_url,
      description: defaults.description,
      inLanguage: defaults.locale || 'en',
    },
  ]);
}

function setMeta(name, content, attribute = 'name') {
  if (!content || typeof document === 'undefined') {
    return;
  }

  let element = document.head.querySelector(`meta[${attribute}="${name}"]`);

  if (!element) {
    element = document.createElement('meta');
    element.setAttribute(attribute, name);
    document.head.appendChild(element);
  }

  element.setAttribute('content', content);
}

function setLink(rel, href) {
  if (!href || typeof document === 'undefined') {
    return;
  }

  let element = document.head.querySelector(`link[rel="${rel}"]`);

  if (!element) {
    element = document.createElement('link');
    element.setAttribute('rel', rel);
    document.head.appendChild(element);
  }

  element.setAttribute('href', href);
}

function setJsonLd(id, data) {
  if (typeof document === 'undefined') {
    return;
  }

  let element = document.getElementById(id);

  if (!element) {
    element = document.createElement('script');
    element.type = 'application/ld+json';
    element.id = id;
    document.head.appendChild(element);
  }

  element.textContent = JSON.stringify(data);
}

function removeJsonLd(id) {
  document.getElementById(id)?.remove();
}

function absoluteUrl(value) {
  if (!value) {
    return null;
  }

  if (/^https?:\/\//i.test(value)) {
    return value;
  }

  const base = defaults.site_url || (typeof window !== 'undefined' ? window.location.origin : '');

  return `${base.replace(/\/$/, '')}/${String(value).replace(/^\//, '')}`;
}

function stripHtml(value) {
  return String(value || '')
    .replace(/<[^>]*>/g, ' ')
    .replace(/\s+/g, ' ')
    .trim();
}

function truncate(value, maxLength) {
  if (value.length <= maxLength) {
    return value;
  }

  return `${value.slice(0, maxLength - 1).trim()}…`;
}
