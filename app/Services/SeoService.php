<?php

namespace App\Services;

use App\Models\Novena;
use App\Models\Prayer;
use App\Models\StaticPage;
use App\Models\SystemSetting;
use Throwable;

class SeoService
{
    public const SETTING_DESCRIPTION = 'seo_default_description';

    public const SETTING_OG_IMAGE = 'seo_og_image';

    public const SETTING_TWITTER = 'seo_twitter_handle';

    public const SETTING_KEYWORDS = 'seo_default_keywords';

    /**
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        $siteUrl = rtrim((string) config('app.url'), '/');
        $siteName = $this->setting('site_name', config('app.name', 'Praise U Lord'));
        $description = $this->setting(
            self::SETTING_DESCRIPTION,
            'Read the Bible, follow daily Mass guides, novenas, prayers, and Catholic feast days on '.$siteName.'.'
        );
        $ogImage = $this->absoluteUrl($this->setting(self::SETTING_OG_IMAGE));

        return [
            'site_name' => $siteName,
            'description' => $description,
            'keywords' => $this->setting(self::SETTING_KEYWORDS, 'bible, catholic, mass guide, novenas, prayers, fiesta calendar'),
            'site_url' => $siteUrl,
            'og_image' => $ogImage,
            'twitter_site' => $this->setting(self::SETTING_TWITTER),
            'locale' => str_replace('_', '-', (string) app()->getLocale()),
        ];
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

    protected function absoluteUrl(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return rtrim((string) config('app.url'), '/').'/'.ltrim($value, '/');
    }
}
