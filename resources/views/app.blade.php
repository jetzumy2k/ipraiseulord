<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $pageTitle = $seo['full_title'] ?? $seo['site_name'] ?? config('app.name', 'Praise U Lord');
        $description = $seo['description'] ?? '';
        $canonical = $seo['canonical'] ?? ($seo['site_url'] ?? url('/'));
        $ogImage = $seo['og_image'] ?? null;
        $robots = $seo['robots'] ?? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
        $ogType = $seo['og_type'] ?? 'website';
        $siteName = $seo['site_name'] ?? config('app.name', 'Praise U Lord');
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $description }}">
    @if(!empty($seo['keywords']))
        <meta name="keywords" content="{{ $seo['keywords'] }}">
    @endif
    <meta name="robots" content="{{ $robots }}">
    <meta name="author" content="{{ $siteName }}">
    <meta name="application-name" content="{{ $siteName }}">
    <link rel="canonical" href="{{ $canonical }}">

    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:locale" content="{{ $seo['locale'] ?? 'en' }}">
    @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
        <meta property="og:image:secure_url" content="{{ $ogImage }}">
        <meta property="og:image:alt" content="{{ $seo['title'] ?? $siteName }}">
        <meta property="og:image:width" content="{{ $seo['og_image_width'] ?? 1200 }}">
        <meta property="og:image:height" content="{{ $seo['og_image_height'] ?? 630 }}">
    @endif

    <meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $description }}">
    @if(!empty($seo['twitter_site']))
        <meta name="twitter:site" content="{{ $seo['twitter_site'] }}">
    @endif
    @if($ogImage)
        <meta name="twitter:image" content="{{ $ogImage }}">
        <meta name="twitter:image:alt" content="{{ $seo['title'] ?? $siteName }}">
    @endif

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => rtrim($seo['site_url'] ?? url('/'), '/'),
            'description' => $description,
            'inLanguage' => $seo['locale'] ?? 'en',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
    </script>

    @php
        $viteReady = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'));
    @endphp
    @if ($viteReady)
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    <div id="app"></div>
    @unless ($viteReady)
        <noscript>
            <div style="max-width: 40rem; margin: 2rem auto; padding: 1.5rem; font-family: system-ui, sans-serif; text-align: center;">
                <h1 style="font-size: 1.25rem; margin-bottom: 0.75rem;">Frontend assets are missing</h1>
                <p style="margin: 0; color: #555;">
                    Run <code>npm run build</code> on your computer, then upload the
                    <code>public/build</code> folder to the server.
                </p>
            </div>
        </noscript>
        <div style="max-width: 40rem; margin: 2rem auto; padding: 1.5rem; font-family: system-ui, sans-serif; text-align: center;">
            <h1 style="font-size: 1.25rem; margin-bottom: 0.75rem;">Frontend assets are missing</h1>
            <p style="margin: 0; color: #555;">
                Run <code>npm run build</code> on your computer, then upload the
                <code>public/build</code> folder to the server.
            </p>
        </div>
    @endunless
</body>
</html>
