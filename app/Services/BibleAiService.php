<?php

namespace App\Services;

use App\Models\AiConversation;
use App\Models\BibleVerse;
use App\Support\SpiritualAdviceFormatter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BibleAiService
{
    public const DEFAULT_LANGUAGE = 'en';

    public const DEFAULT_VERSION = 'RSVCE';

    public function __construct(
        protected OpenAiAdviceService $openAiAdvice,
        protected BibleTopicAdviceService $topicAdvice,
    ) {}

    public function searchVerses(string $query, ?string $bibleVersion = null, int $limit = 10): Collection
    {
        if ($bibleVersion) {
            return $this->runVerseSearch($query, $bibleVersion, $limit);
        }

        $verses = $this->runVerseSearch($query, self::DEFAULT_VERSION, $limit);

        if ($verses->isNotEmpty()) {
            return $verses;
        }

        return $this->runVerseSearch($query, null, $limit, self::DEFAULT_LANGUAGE);
    }

    protected function runVerseSearch(
        string $query,
        ?string $bibleVersion = null,
        int $limit = 10,
        ?string $language = null,
    ): Collection {
        $builder = BibleVerse::query()
            ->with(['chapter.book.version'])
            ->limit($limit);

        $builder->whereHas('chapter.book.version', function ($q) use ($bibleVersion, $language) {
            $q->where('is_active', true);

            if ($bibleVersion) {
                $q->where(function ($q) use ($bibleVersion) {
                    $q->where('abbreviation', $bibleVersion)
                        ->orWhere('name', $bibleVersion);
                });
            } elseif ($language) {
                $q->where('language', $language);
            }
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            $builder->whereFullText('text', $query);
        } else {
            $terms = preg_split('/\s+/', trim($query)) ?: [];

            foreach ($terms as $term) {
                $builder->where('text', 'like', '%'.$term.'%');
            }
        }

        return $builder->get();
    }

    /**
     * @return array{answer: string, answer_sections: array<int, array<string, string>>, references: array<int, array<string, mixed>>, bible_version: string}
     */
    public function generateAdvice(string $question, ?string $bibleVersion = null): array
    {
        $resolvedVersion = $bibleVersion ?: self::DEFAULT_VERSION;

        if ($this->openAiAdvice->isConfigured()) {
            $llmAdvice = $this->openAiAdvice->generateAdvice($question, $bibleVersion);

            if ($llmAdvice !== null) {
                return $llmAdvice;
            }
        }

        $topicAdvice = $this->topicAdvice->generateAdvice($question, $bibleVersion);

        if ($topicAdvice !== null) {
            return $topicAdvice;
        }

        return $this->generateFallbackAdvice($resolvedVersion);
    }

    /**
     * @return array{answer: string, answer_sections: array<int, array<string, string>>, references: array<int, array<string, mixed>>, bible_version: string}
     */
    protected function generateFallbackAdvice(string $bibleVersion): array
    {
        $sections = SpiritualAdviceFormatter::sectionsFromReferences([], []);

        return SpiritualAdviceFormatter::build([], $sections, $bibleVersion);
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function answerSectionsFromAnswer(string $answer): array
    {
        return collect(preg_split('/\n\s*\n/', trim($answer)) ?: [])
            ->map(fn ($text) => trim((string) $text))
            ->filter()
            ->map(fn ($text) => ['type' => 'paragraph', 'text' => $text])
            ->values()
            ->all();
    }

    /**
     * @return array{conversation: AiConversation, answer_sections: array<int, array<string, string>>}
     */
    public function ask(
        string $question,
        ?string $visitorId = null,
        ?int $userId = null,
        ?string $bibleVersion = null
    ): array {
        $result = $this->generateAdvice($question, $bibleVersion);

        $conversation = AiConversation::create([
            'visitor_id' => $visitorId,
            'user_id' => $userId,
            'question' => $question,
            'answer' => $result['answer'],
            'bible_references' => $result['references'],
            'bible_version' => $result['bible_version'],
        ]);

        return [
            'conversation' => $conversation,
            'answer_sections' => $result['answer_sections'],
        ];
    }
}
