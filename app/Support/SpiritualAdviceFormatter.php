<?php

namespace App\Support;

class SpiritualAdviceFormatter
{
    /**
     * @param  array<int, array{reference: string, text: string, version: string|null}>  $references
     * @param  array<int, array<string, string>>  $answerSections
     * @return array{answer: string, answer_sections: array<int, array<string, string>>, references: array<int, array<string, mixed>>, bible_version: string}
     */
    public static function build(array $references, array $answerSections, string $bibleVersion): array
    {
        $answer = collect($answerSections)
            ->pluck('text')
            ->filter()
            ->implode("\n\n");

        return [
            'answer' => $answer,
            'answer_sections' => $answerSections,
            'references' => $references,
            'bible_version' => $bibleVersion,
        ];
    }

    /**
     * @param  array<int, array{reference: string, text: string, version: string|null}>  $references
     * @param  array<int, string>  $guidanceParagraphs
     * @return array<int, array<string, string>>
     */
    public static function sectionsFromReferences(array $references, array $guidanceParagraphs = []): array
    {
        if ($references === []) {
            return [
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
        }

        $sections = [
            [
                'type' => 'intro',
                'text' => 'Thank you for your question. Based on Scripture, here is guidance that may help.',
            ],
        ];

        foreach ($guidanceParagraphs as $paragraph) {
            $sections[] = [
                'type' => 'paragraph',
                'text' => $paragraph,
            ];
        }

        if ($guidanceParagraphs === []) {
            $sections[] = [
                'type' => 'paragraph',
                'text' => 'Your question touches on matters of faith where God\'s Word offers wisdom. The passages below speak to themes related to your inquiry.',
            ];
        }

        $sections[] = [
            'type' => 'scripture',
            'label' => 'Scripture to reflect on',
            'text' => self::scriptureExcerpt($references),
        ];

        $sections[] = [
            'type' => 'paragraph',
            'text' => 'Pray with these Scriptures, asking the Holy Spirit to illuminate their meaning for your situation.',
        ];

        $sections[] = [
            'type' => 'closing',
            'text' => 'May you find peace and clarity in God\'s loving presence.',
        ];

        return $sections;
    }

    /**
     * @param  array<int, array{reference: string, text: string, version: string|null}>  $references
     */
    public static function scriptureExcerpt(array $references, int $maxLength = 280): string
    {
        $excerpt = collect($references)
            ->take(2)
            ->pluck('text')
            ->implode(' ');

        if (mb_strlen($excerpt) > $maxLength) {
            return mb_substr($excerpt, 0, $maxLength - 3).'...';
        }

        return $excerpt;
    }

    /**
     * @param  array<int, array<string, string>>  $sections
     * @return array<int, array<string, string>>
     */
    public static function injectScriptureSection(array $sections, array $references): array
    {
        if ($references === []) {
            return $sections;
        }

        $scriptureSection = [
            'type' => 'scripture',
            'label' => 'Scripture to reflect on',
            'text' => self::scriptureExcerpt($references),
        ];

        $closingIndex = null;

        foreach ($sections as $index => $section) {
            if (($section['type'] ?? '') === 'closing') {
                $closingIndex = $index;
                break;
            }
        }

        if ($closingIndex !== null) {
            array_splice($sections, $closingIndex, 0, [$scriptureSection]);

            return $sections;
        }

        $sections[] = $scriptureSection;

        return $sections;
    }
}
