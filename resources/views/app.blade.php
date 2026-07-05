<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $seo['site_name'] ?? config('app.name', 'Praise U Lord') }}</title>
    <meta name="description" content="{{ $seo['description'] ?? '' }}">
    @if(!empty($seo['keywords']))
        <meta name="keywords" content="{{ $seo['keywords'] }}">
    @endif
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="author" content="{{ $seo['site_name'] ?? config('app.name', 'Praise U Lord') }}">
    <link rel="canonical" href="{{ $seo['site_url'] ?? url('/') }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $seo['site_name'] ?? config('app.name', 'Praise U Lord') }}">
    <meta property="og:title" content="{{ $seo['site_name'] ?? config('app.name', 'Praise U Lord') }}">
    <meta property="og:description" content="{{ $seo['description'] ?? '' }}">
    <meta property="og:url" content="{{ $seo['site_url'] ?? url('/') }}">
    <meta property="og:locale" content="{{ $seo['locale'] ?? 'en' }}">
    @if(!empty($seo['og_image']))
        <meta property="og:image" content="{{ $seo['og_image'] }}">
    @endif
    <meta name="twitter:card" content="{{ !empty($seo['og_image']) ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $seo['site_name'] ?? config('app.name', 'Praise U Lord') }}">
    <meta name="twitter:description" content="{{ $seo['description'] ?? '' }}">
    @if(!empty($seo['twitter_site']))
        <meta name="twitter:site" content="{{ $seo['twitter_site'] }}">
    @endif
    @if(!empty($seo['og_image']))
        <meta name="twitter:image" content="{{ $seo['og_image'] }}">
    @endif
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
