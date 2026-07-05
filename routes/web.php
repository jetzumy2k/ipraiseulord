<?php

use App\Http\Controllers\SeoController;
use App\Services\SeoService;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [SeoController::class, 'sitemap']);
Route::get('/robots.txt', [SeoController::class, 'robots']);

Route::get('/install/{any?}', function () {
    return view('app', [
        'seo' => [
            'site_name' => 'Setup — Praise U Lord',
            'description' => 'Install Praise U Lord',
            'site_url' => url('/install'),
        ],
    ]);
})->where('any', '.*')->middleware('not-installed');

Route::get('/{any?}', function () {
    return view('app', [
        'seo' => app(SeoService::class)->forRequest(request()),
    ]);
})->where('any', '.*');
