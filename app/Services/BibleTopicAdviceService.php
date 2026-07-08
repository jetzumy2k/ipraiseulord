<?php

namespace App\Services;

use App\Support\SpiritualAdviceFormatter;

class BibleTopicAdviceService
{
    public function __construct(protected BiblePassageResolver $passageResolver) {}

    /**
     * @return array{answer: string, answer_sections: array<int, array<string, string>>, references: array<int, array<string, mixed>>, bible_version: string}|null
     */
    public function generateAdvice(string $question, ?string $bibleVersion = null): ?array
    {
        $topic = $this->matchTopic($question);

        if ($topic === null) {
            return null;
        }

        $resolvedVersion = $bibleVersion ?: BibleAiService::DEFAULT_VERSION;
        $references = $this->passageResolver->resolveMany($topic['references'], $resolvedVersion);

        if ($references === []) {
            return null;
        }

        $sections = SpiritualAdviceFormatter::sectionsFromReferences(
            $references,
            $topic['guidance'] ?? [],
        );

        return SpiritualAdviceFormatter::build($references, $sections, $resolvedVersion);
    }

    /**
     * @return array{references: array<int, string>, guidance: array<int, string>}|null
     */
    public function matchTopic(string $question): ?array
    {
        $normalizedQuestion = mb_strtolower($question);
        $bestTopic = null;
        $bestScore = 0;

        foreach (config('bible_ai_topics.topics', []) as $topic) {
            $score = 0;

            foreach ($topic['keywords'] as $keyword) {
                if ($this->questionContainsKeyword($normalizedQuestion, mb_strtolower($keyword))) {
                    $score++;
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestTopic = $topic;
            }
        }

        if ($bestTopic === null || $bestScore === 0) {
            return null;
        }

        return [
            'references' => $bestTopic['references'],
            'guidance' => $bestTopic['guidance'] ?? [],
        ];
    }

    protected function questionContainsKeyword(string $normalizedQuestion, string $keyword): bool
    {
        if ($keyword === '') {
            return false;
        }

        $pattern = '/\b'.preg_quote($keyword, '/').'\b/u';

        return (bool) preg_match($pattern, $normalizedQuestion);
    }
}
