<?php

namespace App\Services;

use App\Models\AiConversation;
use App\Models\BibleVerse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BibleAiService
{
    public function searchVerses(string $query, ?string $bibleVersion = null, int $limit = 10): Collection
    {
        $builder = BibleVerse::query()
            ->with(['chapter.book.version'])
            ->limit($limit);

        if ($bibleVersion) {
            $builder->whereHas('chapter.book.version', function ($q) use ($bibleVersion) {
                $q->where('abbreviation', $bibleVersion)
                    ->orWhere('name', $bibleVersion);
            });
        }

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
     * @return array{answer: string, answer_sections: array<int, array<string, string>>, references: array<int, array<string, mixed>>}
     */
    public function generateAdvice(string $question, ?string $bibleVersion = null): array
    {
        $verses = $this->searchVerses($question, $bibleVersion, 5);

        $references = $verses->map(function (BibleVerse $verse) {
            $book = $verse->chapter?->book;
            $chapter = $verse->chapter;

            return [
                'reference' => sprintf(
                    '%s %d:%d',
                    $book?->name ?? 'Unknown',
                    $chapter?->chapter_number ?? 0,
                    $verse->verse_number
                ),
                'text' => $verse->text,
                'version' => $book?->version?->abbreviation,
            ];
        })
            ->unique('reference')
            ->values()
            ->all();

        if ($references === []) {
            $answerSections = [
                [
                    'type' => 'paragraph',
                    'text' => 'I could not find specific Scripture passages matching your question.',
                ],
                [
                    'type' => 'paragraph',
                    'text' => 'Consider rephrasing your question or consulting a priest or spiritual director for guidance.',
                ],
                [
                    'type' => 'closing',
                    'text' => 'Remember that God\'s love and mercy are always available to you through prayer.',
                ],
            ];
        } else {
            $scriptureExcerpt = collect($references)
                ->take(2)
                ->pluck('text')
                ->implode(' ');

            if (mb_strlen($scriptureExcerpt) > 280) {
                $scriptureExcerpt = mb_substr($scriptureExcerpt, 0, 277).'...';
            }

            $answerSections = [
                [
                    'type' => 'intro',
                    'text' => 'Thank you for your question. Based on Scripture, here is guidance that may help.',
                ],
                [
                    'type' => 'paragraph',
                    'text' => 'Your question touches on matters of faith where God\'s Word offers wisdom. The passages below speak to themes related to your inquiry.',
                ],
                [
                    'type' => 'scripture',
                    'label' => 'Scripture to reflect on',
                    'text' => $scriptureExcerpt,
                ],
                [
                    'type' => 'paragraph',
                    'text' => 'Pray with these Scriptures, asking the Holy Spirit to illuminate their meaning for your situation.',
                ],
                [
                    'type' => 'closing',
                    'text' => 'May you find peace and clarity in God\'s loving presence.',
                ],
            ];
        }

        $answer = collect($answerSections)
            ->pluck('text')
            ->filter()
            ->implode("\n\n");

        return [
            'answer' => $answer,
            'answer_sections' => $answerSections,
            'references' => $references,
        ];
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
            'bible_version' => $bibleVersion,
        ]);

        return [
            'conversation' => $conversation,
            'answer_sections' => $result['answer_sections'],
        ];
    }
}
