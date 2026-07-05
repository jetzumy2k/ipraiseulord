<?php

namespace App\Services;

use App\Models\Novena;
use App\Models\PageBanner;
use App\Models\Prayer;
use App\Models\StaticPage;
use App\Models\SystemSetting;
use App\Support\PublicRouteSeo;
use Illuminate\Http\Request;
use Throwable;

class SeoService
{
    public const SETTING_DESCRIPTION = 'seo_default_description';

    public const SETTING_OG_IMAGE = 'seo_og_image';

    public const SETTING_TWITTER = 'seo_twitter_handle';

    public const SETTING_KEYWORDS = 'seo_default_keywords';

    public const DEFAULT_OG_IMAGE = '/images/og-default.png';

    /**
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        return $this->buildMeta([
            'title' => 'Home',
            'description' => $this->defaultDescription(),
            'path' => '/',
            'route_key' => 'home',
        ]);
    }

    /**
     * Build SEO payload for the current HTTP request (used by crawlers and initial HTML).
     *
     * @return array<string, mixed>
     */
    public function forRequest(Request $request): array
    {
        $path = '/'.ltrim($request->path(), '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        if (str_starts_with($path, '/admin')) {
            return $this->buildMeta([
                'title' => 'Admin',
                'description' => 'Administration area',
                'path' => $path,
                'robots' => 'noindex, nofollow',
            ], $request);
        }

        if ($route = PublicRouteSeo::findByPath($path)) {
            $meta = [
                'title' => $route['title'],
                'description' => $route['description'],
                'path' => $route['path'],
                'route_key' => $route['key'],
                'og_type' => $route['og_type'] ?? 'website',
                'robots' => $route['robots'] ?? null,
            ];

            if (! empty($route['static_slug'])) {
                $page = StaticPage::query()
                    ->where('slug', $route['static_slug'])
                    ->where('is_published', true)
                    ->first(['title', 'meta_description']);

                if ($page) {
                    $meta['title'] = $page->title;
                    if ($page->meta_description) {
                        $meta['description'] = $page->meta_description;
                    }
                }
            } elseif (! empty($route['page_route'])) {
                $page = StaticPage::query()
                    ->where('page_route', $route['page_route'])
                    ->where('is_published', true)
                    ->first(['title', 'meta_description']);

                if ($page?->meta_description) {
                    $meta['description'] = $page->meta_description;
                }
            }

            return $this->buildMeta($meta, $request);
        }

        if (preg_match('#^/novenas/([^/]+)$#', $path, $matches)) {
            $novena = Novena::query()
                ->where('slug', $matches[1])
                ->where('is_active', true)
                ->first(['title', 'description']);

            if ($novena) {
                return $this->buildMeta([
                    'title' => $novena->title,
                    'description' => $this->excerpt($novena->description, 'Pray a nine-day Catholic novena on '.$this->siteName().'.'),
                    'path' => $path,
                    'route_key' => 'novena-detail',
                    'og_type' => 'article',
                ], $request);
            }
        }

        if (preg_match('#^/prayers/([^/]+)$#', $path, $matches)) {
            $prayer = Prayer::query()
                ->where('slug', $matches[1])
                ->where('is_active', true)
                ->first(['title', 'description']);

            if ($prayer) {
                return $this->buildMeta([
                    'title' => $prayer->title,
                    'description' => $this->excerpt($prayer->description, 'Read and pray this Catholic prayer on '.$this->siteName().'.'),
                    'path' => $path,
                    'route_key' => 'prayer-detail',
                    'og_type' => 'article',
                ], $request);
            }
        }

        if (preg_match('#^/bible/([^/]+)/([^/]+)/(\d+)$#', $path, $matches)) {
            $book = str_replace('-', ' ', ucwords($matches[2], '-'));

            return $this->buildMeta([
                'title' => "{$book} Chapter {$matches[3]}",
                'description' => "Read {$book} chapter {$matches[3]} online in the {$matches[1]} Bible translation on ".$this->siteName().'.',
                'path' => $path,
                'route_key' => 'bible-chapter',
            ], $request);
        }

        return $this->buildMeta([
            'title' => $this->siteName(),
            'description' => $this->defaultDescription(),
            'path' => $path,
        ], $request);
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function sitemapEntries(): array
    {
        $base = rtrim((string) config('app.url'), '/');
        $now = now()->toAtomString();

        $entries = [
            $this->entry($base.'/', 'daily', '1.0', $now),
            $this->entry($base.'/bible', 'weekly', '0.9', $now),
            $this->entry($base.'/mass-guide', 'daily', '0.9', $now),
            $this->entry($base.'/fiesta-calendar', 'weekly', '0.8', $now),
            $this->entry($base.'/novenas', 'weekly', '0.8', $now),
            $this->entry($base.'/prayers', 'weekly', '0.8', $now),
            $this->entry($base.'/ai-advice', 'weekly', '0.7', $now),
            $this->entry($base.'/about', 'monthly', '0.6', $now),
            $this->entry($base.'/contact', 'monthly', '0.6', $now),
            $this->entry($base.'/privacy', 'yearly', '0.3', $now),
            $this->entry($base.'/terms', 'yearly', '0.3', $now),
        ];

        if ($this->donationsPubliclyVisible()) {
            $entries[] = $this->entry($base.'/donate', 'monthly', '0.5', $now);
        }

        foreach (Novena::query()->where('is_active', true)->orderBy('title')->get(['slug', 'updated_at']) as $novena) {
            $entries[] = $this->entry(
                $base.'/novenas/'.$novena->slug,
                'monthly',
                '0.7',
                optional($novena->updated_at)->toAtomString() ?? $now
            );
        }

        foreach (Prayer::query()->where('is_active', true)->orderBy('title')->get(['slug', 'updated_at']) as $prayer) {
            $entries[] = $this->entry(
                $base.'/prayers/'.$prayer->slug,
                'monthly',
                '0.7',
                optional($prayer->updated_at)->toAtomString() ?? $now
            );
        }

        foreach ($this->publishedStaticPageRoutes() as $route) {
            $entries[] = $this->entry($base.$route['path'], 'monthly', '0.5', $route['lastmod']);
        }

        return $entries;
    }

    public function robotsTxt(): string
    {
        $siteUrl = rtrim((string) config('app.url'), '/');

        return implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /api',
            '',
            'Sitemap: '.$siteUrl.'/sitemap.xml',
        ]);
    }

    public function setting(string $key, ?string $default = null): ?string
    {
        try {
            $value = SystemSetting::query()->where('key', $key)->value('value');
        } catch (Throwable) {
            return $default;
        }

        if ($value === null || $value === '') {
            return $default;
        }

        return $value;
    }

    /**
     * @param  array<string, mixed>  $meta
     * @return array<string, mixed>
     */
    protected function buildMeta(array $meta, ?Request $request = null): array
    {
        $siteName = $this->siteName();
        $siteUrl = $this->siteBaseUrl($request);
        $path = $meta['path'] ?? '/';
        $pageTitle = trim((string) ($meta['title'] ?? $siteName));
        $description = $this->excerpt(
            (string) ($meta['description'] ?? $this->defaultDescription()),
            $this->defaultDescription(),
        );
        $routeKey = $meta['route_key'] ?? null;
        $ogImage = $this->resolveOgImage($routeKey, $request);
        $canonical = $siteUrl.$path;
        $fullTitle = $this->fullTitle($pageTitle, $siteName, $path);

        return [
            'site_name' => $siteName,
            'title' => $pageTitle,
            'full_title' => $fullTitle,
            'description' => $description,
            'keywords' => $this->setting(
                self::SETTING_KEYWORDS,
                'bible, catholic, mass guide, novenas, prayers, fiesta calendar, scripture, devotionals'
            ),
            'site_url' => $canonical,
            'canonical' => $canonical,
            'og_image' => $ogImage,
            'og_image_width' => 1200,
            'og_image_height' => 630,
            'og_type' => $meta['og_type'] ?? 'website',
            'twitter_site' => $this->setting(self::SETTING_TWITTER),
            'locale' => str_replace('_', '-', (string) app()->getLocale()),
            'robots' => $meta['robots'] ?? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
        ];
    }

    protected function fullTitle(string $pageTitle, string $siteName, string $path): string
    {
        if ($path === '/' || $pageTitle === 'Home') {
            return $siteName.' — Mass Guide, Novenas, Prayers & Bible';
        }

        if ($pageTitle === $siteName) {
            return $siteName;
        }

        return $pageTitle.' | '.$siteName;
    }

    protected function resolveOgImage(?string $routeKey, ?Request $request = null): string
    {
        $configured = $this->absoluteUrl($this->setting(self::SETTING_OG_IMAGE), $request);
        if ($configured) {
            return $configured;
        }

        if ($routeKey) {
            $bannerImage = PageBanner::query()
                ->where('route_name', $routeKey)
                ->where('is_active', true)
                ->value('image_path');

            $bannerUrl = $this->absoluteUrl($bannerImage, $request);
            if ($bannerUrl) {
                return $bannerUrl;
            }
        }

        $homeBanner = PageBanner::query()
            ->where('route_name', 'home')
            ->where('is_active', true)
            ->value('image_path');

        $homeBannerUrl = $this->absoluteUrl($homeBanner, $request);
        if ($homeBannerUrl) {
            return $homeBannerUrl;
        }

        return $this->absoluteUrl(self::DEFAULT_OG_IMAGE, $request) ?? self::DEFAULT_OG_IMAGE;
    }

    protected function siteName(): string
    {
        return $this->setting('site_name', config('app.name', 'Praise U Lord')) ?? 'Praise U Lord';
    }

    protected function defaultDescription(): string
    {
        $siteName = $this->siteName();

        return $this->setting(
            self::SETTING_DESCRIPTION,
            'Read the Bible, follow daily Mass guides, novenas, prayers, and Catholic feast days on '.$siteName.'.'
        ) ?? 'Read the Bible, follow daily Mass guides, novenas, prayers, and Catholic feast days.';
    }

    protected function siteBaseUrl(?Request $request = null): string
    {
        if ($request) {
            return rtrim($request->getSchemeAndHttpHost(), '/');
        }

        return rtrim((string) config('app.url'), '/');
    }

    protected function excerpt(?string $value, string $fallback, int $maxLength = 160): string
    {
        $text = trim(strip_tags((string) $value));
        $text = preg_replace('/\s+/', ' ', $text) ?? '';
        $text = $text !== '' ? $text : $fallback;

        if (strlen($text) <= $maxLength) {
            return $text;
        }

        return rtrim(substr($text, 0, $maxLength - 1)).'…';
    }

    protected function donationsPubliclyVisible(): bool
    {
        try {
            return DonationSettingsService::isGloballyEnabled()
                && DonationSettingsService::publicDonations()->isNotEmpty();
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @return array<int, array{path: string, lastmod: string}>
     */
    protected function publishedStaticPageRoutes(): array
    {
        $routesBySlug = [
            'about-us' => '/about',
            'privacy-policy' => '/privacy',
            'terms-and-conditions' => '/terms',
        ];

        $routes = [];

        foreach (StaticPage::query()->where('is_published', true)->get(['slug', 'updated_at']) as $page) {
            $path = $routesBySlug[$page->slug] ?? null;

            if (! $path) {
                continue;
            }

            $routes[] = [
                'path' => $path,
                'lastmod' => optional($page->updated_at)->toAtomString() ?? now()->toAtomString(),
            ];
        }

        return $routes;
    }

    /**
     * @return array{loc: string, changefreq: string, priority: string, lastmod: string}
     */
    protected function entry(string $loc, string $changefreq, string $priority, string $lastmod): array
    {
        return [
            'loc' => $loc,
            'changefreq' => $changefreq,
            'priority' => $priority,
            'lastmod' => $lastmod,
        ];
    }

    protected function absoluteUrl(?string $value, ?Request $request = null): ?string
    {
        if (! $value) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return $this->siteBaseUrl($request).'/'.ltrim($value, '/');
    }
}
