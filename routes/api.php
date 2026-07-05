<?php

use App\Http\Controllers\Api\InstallController;
use App\Http\Controllers\Api\AdInvoiceController;
use App\Http\Controllers\Api\AdvertisementController;
use App\Http\Controllers\Api\AiAdviceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BibleBookController;
use App\Http\Controllers\Api\BibleChapterController;
use App\Http\Controllers\Api\BibleVerseController;
use App\Http\Controllers\Api\BibleVersionController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\DailyPsalmController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DonationSettingController;
use App\Http\Controllers\Api\FiestaController;
use App\Http\Controllers\Api\MassGuideController;
use App\Http\Controllers\Api\NovenaController;
use App\Http\Controllers\Api\PageBannerController;
use App\Http\Controllers\Api\PageVisitController;
use App\Http\Controllers\Api\PrayerController;
use App\Http\Controllers\Api\ProverbController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\SocialMediaSettingController;
use App\Http\Controllers\Api\StaticPageController;
use App\Http\Controllers\Api\SystemSettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Setup Wizard (only before installation)
|--------------------------------------------------------------------------
*/

Route::prefix('install')->middleware('not-installed')->group(function () {
    Route::get('status', [InstallController::class, 'status']);
    Route::get('requirements', [InstallController::class, 'requirements']);
    Route::post('test-database', [InstallController::class, 'testDatabase']);
    Route::post('run', [InstallController::class, 'run']);
});

/*
|--------------------------------------------------------------------------
| Public Routes (no authentication)
|--------------------------------------------------------------------------
*/

Route::prefix('public')->group(function () {
    Route::get('proverb/random', [PublicController::class, 'randomProverb']);
    Route::get('psalm/random', [PublicController::class, 'randomPsalm']);
    Route::get('mass/today', [PublicController::class, 'todaysMass']);
    Route::get('mass/guide', [PublicController::class, 'massGuide']);
    Route::get('ads/{placement}', [PublicController::class, 'adsByPlacement']);
    Route::get('settings', [PublicController::class, 'settings']);
    Route::get('banners', [PublicController::class, 'pageBanners']);
    Route::get('pages', [PublicController::class, 'staticPages']);
    Route::get('pages/route/{route}', [PublicController::class, 'staticPageByRoute']);
    Route::get('pages/{slug}', [PublicController::class, 'staticPage']);
    Route::post('visits', [PublicController::class, 'trackVisit']);
    Route::post('contact', [PublicController::class, 'submitContact']);

    Route::post('ai/ask', [AiAdviceController::class, 'ask']);
    Route::get('ai/search', [AiAdviceController::class, 'searchVerses']);

    Route::get('bible/languages', [PublicController::class, 'bibleLanguages']);
    Route::get('bible/versions', [PublicController::class, 'bibleVersions']);
    Route::get('bible/verse/random/{scope}', [PublicController::class, 'randomBibleVerse'])
        ->where('scope', 'psalms|old-testament|new-testament|any');
    Route::get('bible/{version}/books', [PublicController::class, 'bibleBooks']);
    Route::get('bible/{version}/{book}/{chapter}/meta', [PublicController::class, 'bibleChapterMeta'])->whereNumber('chapter');
    Route::get('bible/{version}/{book}/{chapter}', [PublicController::class, 'bibleChapter'])->whereNumber('chapter');
    Route::get('novenas', [PublicController::class, 'novenas']);
    Route::get('novenas/{slug}', [PublicController::class, 'novena']);
    Route::get('prayers', [PublicController::class, 'prayers']);
    Route::get('prayers/{slug}', [PublicController::class, 'prayer']);
    Route::get('fiestas/calendar', [PublicController::class, 'fiestaCalendar']);
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes (auth:sanctum + superadmin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'superadmin'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index']);

    Route::get('advertisements/sales-report', [AdvertisementController::class, 'salesReport']);

    $resources = [
        'bible-versions' => BibleVersionController::class,
        'bible-books' => BibleBookController::class,
        'bible-chapters' => BibleChapterController::class,
        'bible-verses' => BibleVerseController::class,
        'mass-guides' => MassGuideController::class,
        'fiestas' => FiestaController::class,
        'novenas' => NovenaController::class,
        'prayers' => PrayerController::class,
        'proverbs' => ProverbController::class,
        'daily-psalms' => DailyPsalmController::class,
        'advertisements' => AdvertisementController::class,
        'ad-invoices' => AdInvoiceController::class,
        'social-media-settings' => SocialMediaSettingController::class,
        'system-settings' => SystemSettingController::class,
        'page-banners' => PageBannerController::class,
        'donation-settings' => DonationSettingController::class,
        'static-pages' => StaticPageController::class,
        'contact-messages' => ContactMessageController::class,
        'page-visits' => PageVisitController::class,
        'ai-conversations' => AiAdviceController::class,
    ];

    foreach ($resources as $uri => $controller) {
        Route::get("{$uri}/export", [$controller, 'export']);
        Route::post("{$uri}/import", [$controller, 'import']);
        Route::post("{$uri}/{id}/restore", [$controller, 'restore'])->whereNumber('id');
        Route::delete("{$uri}/{id}/force", [$controller, 'forceDelete'])->whereNumber('id');

        if ($uri === 'donation-settings') {
            Route::get("{$uri}/status", [$controller, 'status']);
            Route::post("{$uri}/toggle-global", [$controller, 'toggleGlobal']);
            Route::post("{$uri}/bulk-active", [$controller, 'bulkActive']);
        }

        if ($uri === 'static-pages') {
            Route::get("{$uri}/page-routes", [$controller, 'pageRoutes']);
        }

        Route::apiResource($uri, $controller);
    }
});
