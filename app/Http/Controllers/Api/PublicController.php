<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\BibleBook;
use App\Models\BibleChapter;
use App\Models\BibleVersion;
use App\Models\ContactMessage;
use App\Models\DailyPsalm;
use App\Models\MassGuide;
use App\Models\Novena;
use App\Models\PageBanner;
use App\Models\Prayer;
use App\Models\Proverb;
use App\Models\SocialMediaSetting;
use App\Models\StaticPage;
use App\Models\SystemSetting;
use App\Services\AnalyticsService;
use App\Services\DonationSettingsService;
use App\Services\FiestaDateCalculator;
use App\Services\MassGuideFetchService;
use App\Services\MassOrderService;
use App\Services\NovenaGuideService;
use App\Services\RandomBibleVerseService;
use App\Services\SeoService;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function __construct(
        protected AnalyticsService $analytics,
        protected RandomBibleVerseService $randomBibleVerses,
    ) {
    }

    public function randomProverb(): JsonResponse
    {
        $proverb = Proverb::query()
            ->where('is_active', true)
            ->inRandomOrder()
            ->first();

        if (! $proverb) {
            return response()->json(['message' => 'No proverbs available.'], 404);
        }

        $proverb->increment('visit_count');

        return response()->json($proverb);
    }

    public function randomPsalm(): JsonResponse
    {
        $psalm = DailyPsalm::query()
            ->where('is_active', true)
            ->inRandomOrder()
            ->first();

        if (! $psalm) {
            return response()->json(['message' => 'No psalms available.'], 404);
        }

        $psalm->increment('visit_count');

        return response()->json($psalm);
    }

    public function randomBibleVerse(string $scope): JsonResponse
    {
        try {
            $verse = $scope === 'any'
                ? $this->randomBibleVerses->randomAny()
                : $this->randomBibleVerses->random($scope);
        } catch (\InvalidArgumentException) {
            return response()->json(['message' => 'Invalid verse scope.'], 422);
        }

        if (! $verse) {
            return response()->json(['message' => 'No verses available for this section.'], 404);
        }

        return response()->json($verse);
    }

    public function pageBanners(): JsonResponse
    {
        $banners = PageBanner::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['route_name', 'label', 'image_path', 'overlay_opacity']);

        return response()->json($banners->keyBy('route_name'));
    }

    public function todaysMass(): JsonResponse
    {
        return $this->massGuide(request());
    }

    public function massGuide(Request $request): JsonResponse
    {
        $dateInput = $request->input('date', today()->toDateString());

        try {
            $date = \Carbon\Carbon::parse($dateInput)->toDateString();
        } catch (\Throwable) {
            return response()->json(['message' => 'Invalid date format. Use YYYY-MM-DD.'], 422);
        }

        app(MassGuideFetchService::class)->ensureYear((int) date('Y', strtotime($date)));

        $mass = MassGuide::query()
            ->whereDate('liturgical_date', $date)
            ->first();

        if (! $mass) {
            return response()->json(['message' => 'No mass guide is available for this date.'], 404);
        }

        $prevDate = MassGuide::query()
            ->whereDate('liturgical_date', '<', $date)
            ->orderByDesc('liturgical_date')
            ->value('liturgical_date');

        $nextDate = MassGuide::query()
            ->whereDate('liturgical_date', '>', $date)
            ->orderBy('liturgical_date')
            ->value('liturgical_date');

        $orderOfMass = app(MassOrderService::class)->build($mass);

        return response()->json([
            ...$mass->toArray(),
            'liturgical_date' => $mass->liturgical_date->toDateString(),
            'prev_date' => $prevDate ? (\is_string($prevDate) ? $prevDate : $prevDate->toDateString()) : null,
            'next_date' => $nextDate ? (\is_string($nextDate) ? $nextDate : $nextDate->toDateString()) : null,
            'order_of_mass' => $orderOfMass,
        ]);
    }

    public function adsByPlacement(string $placement): JsonResponse
    {
        $today = today()->toDateString();

        $ads = Advertisement::query()
            ->where('is_active', true)
            ->where(function ($query) use ($today) {
                $query->whereNull('start_date')->orWhereDate('start_date', '<=', $today);
            })
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
            })
            ->whereJsonContains('placements', $placement)
            ->get();

        Advertisement::whereIn('id', $ads->pluck('id'))->increment('impression_count');

        return response()->json($ads);
    }

    public function settings(): JsonResponse
    {
        return response()->json([
            'system' => SystemSetting::all()->groupBy('group'),
            'social_media' => SocialMediaSetting::query()->where('is_active', true)->get(),
            'donations_enabled' => DonationSettingsService::isGloballyEnabled(),
            'donations' => DonationSettingsService::publicDonations(),
            'seo' => app(SeoService::class)->defaults(),
            'weather' => [
                'temperature' => app(WeatherService::class)->getDisplayTemperature(),
            ],
        ]);
    }

    public function staticPages(): JsonResponse
    {
        $pages = StaticPage::query()
            ->where('is_published', true)
            ->orderBy('title')
            ->get(['id', 'slug', 'page_route', 'title', 'meta_description']);

        return response()->json($pages);
    }

    public function staticPage(string $slug): JsonResponse
    {
        $page = StaticPage::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return response()->json($page);
    }

    public function staticPageByRoute(string $route): JsonResponse
    {
        if (! \App\Support\PageRoutes::isValid($route)) {
            return response()->json(['message' => 'Invalid page route.'], 422);
        }

        $page = StaticPage::query()
            ->where('page_route', $route)
            ->where('is_published', true)
            ->first();

        if (! $page) {
            return response()->json(['message' => 'Page content not found.'], 404);
        }

        return response()->json($page);
    }

    public function trackVisit(Request $request): JsonResponse
    {
        $data = $request->validate([
            'visitor_id' => ['nullable', 'string', 'max:255'],
            'page_type' => ['required', 'string', 'max:255'],
            'page_id' => ['nullable', 'integer'],
            'page_slug' => ['nullable', 'string', 'max:255'],
            'page_title' => ['nullable', 'string', 'max:255'],
        ]);

        $visit = $this->analytics->track([
            ...$data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'visited_at' => now(),
        ]);

        return response()->json($visit, 201);
    }

    public function submitContact(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $message = ContactMessage::create($data);

        return response()->json($message, 201);
    }

    public function bibleLanguages(): JsonResponse
    {
        $languages = BibleVersion::query()
            ->where('is_active', true)
            ->select('language')
            ->distinct()
            ->orderBy('language')
            ->pluck('language');

        return response()->json(
            $languages->map(fn (string $code) => [
                'code' => $code,
                'label' => $this->bibleLanguageLabel($code),
                'version_count' => BibleVersion::query()
                    ->where('is_active', true)
                    ->where('language', $code)
                    ->count(),
            ])->values()
        );
    }

    public function bibleVersions(Request $request): JsonResponse
    {
        $query = BibleVersion::query()
            ->where('is_active', true)
            ->orderBy('language')
            ->orderBy('name');

        if ($request->filled('language')) {
            $query->where('language', $request->string('language'));
        }

        return response()->json($query->get());
    }

    public function bibleBooks(string $version): JsonResponse
    {
        $bibleVersion = BibleVersion::query()
            ->where('is_active', true)
            ->where(function ($query) use ($version) {
                $query->where('abbreviation', $version)
                    ->orWhereRaw('LOWER(abbreviation) = ?', [strtolower($version)]);
            })
            ->firstOrFail();

        $books = $bibleVersion->books()->orderBy('book_order')->get();

        return response()->json($books);
    }

    public function bibleChapterMeta(string $version, string $book, int $chapter): JsonResponse
    {
        [, , $chapterModel] = $this->resolveBibleChapter($version, $book, $chapter);

        $verseCount = $chapterModel->verse_count ?: $chapterModel->verses()->count();

        return response()->json([
            'chapter_number' => $chapterModel->chapter_number,
            'verse_count' => $verseCount,
        ]);
    }

    public function bibleChapter(string $version, string $book, int $chapter): JsonResponse
    {
        [$bibleVersion, $bibleBook, $chapterModel] = $this->resolveBibleChapter($version, $book, $chapter, true);

        $prevChapter = BibleChapter::query()
            ->where('bible_book_id', $bibleBook->id)
            ->where('chapter_number', '<', $chapterModel->chapter_number)
            ->orderByDesc('chapter_number')
            ->value('chapter_number');

        $nextChapter = BibleChapter::query()
            ->where('bible_book_id', $bibleBook->id)
            ->where('chapter_number', '>', $chapterModel->chapter_number)
            ->orderBy('chapter_number')
            ->value('chapter_number');

        if (! $prevChapter) {
            $prevBook = BibleBook::query()
                ->where('bible_version_id', $bibleVersion->id)
                ->where('book_order', '<', $bibleBook->book_order)
                ->orderByDesc('book_order')
                ->first();

            if ($prevBook) {
                $prevChapter = $prevBook->chapters()->max('chapter_number');
            }
        }

        if (! $nextChapter) {
            $nextBook = BibleBook::query()
                ->where('bible_version_id', $bibleVersion->id)
                ->where('book_order', '>', $bibleBook->book_order)
                ->orderBy('book_order')
                ->first();

            if ($nextBook) {
                $nextChapter = 1;
            }
        }

        return response()->json([
            ...$chapterModel->toArray(),
            'prev_chapter' => $prevChapter,
            'next_chapter' => $nextChapter,
        ]);
    }

    /**
     * @return array{0: BibleVersion, 1: BibleBook, 2: BibleChapter}
     */
    protected function resolveBibleChapter(string $version, string $book, int $chapter, bool $withVerses = false): array
    {
        $bibleVersion = BibleVersion::query()
            ->where('is_active', true)
            ->where(function ($query) use ($version) {
                $query->where('abbreviation', $version)
                    ->orWhereRaw('LOWER(abbreviation) = ?', [strtolower($version)]);
            })
            ->firstOrFail();

        $bibleBook = $bibleVersion->books()
            ->where(function ($query) use ($book) {
                $query->where('abbreviation', $book)
                    ->orWhereRaw('LOWER(abbreviation) = ?', [strtolower($book)])
                    ->orWhereRaw('LOWER(name) = ?', [strtolower(str_replace('-', ' ', $book))]);
            })
            ->firstOrFail();

        $chapterQuery = BibleChapter::query()
            ->where('bible_book_id', $bibleBook->id)
            ->where('chapter_number', $chapter);

        if ($withVerses) {
            $chapterQuery->with(['verses' => fn ($q) => $q->orderBy('verse_number'), 'book']);
        }

        $chapterModel = $chapterQuery->firstOrFail();

        return [$bibleVersion, $bibleBook, $chapterModel];
    }

    protected function bibleLanguageLabel(string $code): string
    {
        return match ($code) {
            'en' => 'English',
            'ar' => 'Arabic',
            'zh' => 'Chinese',
            'de' => 'German',
            'el' => 'Greek',
            'eo' => 'Esperanto',
            'es' => 'Spanish',
            'fi' => 'Finnish',
            'fr' => 'French',
            'ko' => 'Korean',
            'pt' => 'Portuguese',
            'ro' => 'Romanian',
            'ru' => 'Russian',
            'vi' => 'Vietnamese',
            'tl' => 'Tagalog / Filipino',
            'ceb' => 'Cebuano',
            default => strtoupper($code),
        };
    }

    public function fiestaCalendar(Request $request): JsonResponse
    {
        $year = max(1900, min(2100, (int) $request->input('year', now()->year)));
        $month = $request->filled('month')
            ? max(1, min(12, (int) $request->input('month')))
            : null;
        $category = $request->input('category');
        $calculator = app(FiestaDateCalculator::class);

        if ($month) {
            return response()->json([
                'year' => $year,
                'month' => $month,
                'events' => $calculator->eventsForMonth($year, $month, $category ?: null),
            ]);
        }

        return response()->json([
            'year' => $year,
            'events' => $calculator->eventsForYear($year, $category ?: null),
        ]);
    }

    public function novenas(): JsonResponse
    {
        $novenas = Novena::query()
            ->where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title', 'slug', 'category', 'patron_saint', 'description', 'duration_days']);

        return response()->json($novenas);
    }

    public function novena(string $slug): JsonResponse
    {
        $novena = Novena::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with('days')
            ->firstOrFail();

        $novena->increment('visit_count');

        $guide = app(NovenaGuideService::class)->build($novena);

        return response()->json([
            ...$novena->toArray(),
            'guide' => $guide,
        ]);
    }

    public function prayers(): JsonResponse
    {
        $prayers = Prayer::query()
            ->where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title', 'slug', 'category', 'description']);

        return response()->json($prayers);
    }

    public function prayer(string $slug): JsonResponse
    {
        $prayer = Prayer::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $prayer->increment('visit_count');

        return response()->json($prayer);
    }
}
