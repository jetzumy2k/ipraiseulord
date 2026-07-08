<?php

namespace App\Services;

use App\Support\SpiritualAdviceFormatter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiAdviceService
{
    public function __construct(protected BiblePassageResolver $passageResolver) {}

    public function isConfigured(): bool
    {
        return filled(config('services.openai.api_key'));
    }

    /**
     * @return array{answer: string, answer_sections: array<int, array<string, string>>, references: array<int, array<string, mixed>>, bible_version: string}|null
     */
    public function generateAdvice(string $question, ?string $bibleVersion = null): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $resolvedVersion = $bibleVersion ?: BibleAiService::DEFAULT_VERSION;
        $payload = $this->requestAdvicePayload($question, $resolvedVersion);

        if ($payload === null) {
            return null;
        }

        $references = $this->passageResolver->resolveMany($payload['references'], $resolvedVersion);

        if ($references === []) {
            return null;
        }

        $sections = SpiritualAdviceFormatter::injectScriptureSection(
            $this->normalizeSections($payload['answer_sections']),
            $references,
        );

        return SpiritualAdviceFormatter::build($references, $sections, $resolvedVersion);
    }

    /**
     * @return array{references: array<int, string>, answer_sections: array<int, array<string, string>>}|null
     */
    protected function requestAdvicePayload(string $question, string $bibleVersion): ?array
    {
        try {
            $response = Http::withToken(config('services.openai.api_key'))
                ->timeout((int) config('services.openai.timeout', 30))
                ->acceptJson()
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-4o-mini'),
                    'temperature' => 0.4,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $this->systemPrompt($bibleVersion),
                        ],
                        [
                            'role' => 'user',
                            'content' => $question,
                        ],
                    ],
                ]);

            if (! $response->successful()) {
                Log::warning('OpenAI advice request failed', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);

                return null;
            }

            $content = data_get($response->json(), 'choices.0.message.content');

            if (! is_string($content) || trim($content) === '') {
                return null;
            }

            $decoded = json_decode($content, true);

            if (! is_array($decoded)) {
                return null;
            }

            $references = collect($decoded['references'] ?? [])
                ->filter(fn ($reference) => is_string($reference) && trim($reference) !== '')
                ->map(fn (string $reference) => trim($reference))
                ->unique()
                ->values()
                ->take(6)
                ->all();

            $sections = collect($decoded['answer_sections'] ?? [])
                ->filter(fn ($section) => is_array($section) && filled($section['text'] ?? null))
                ->map(function (array $section) {
                    return [
                        'type' => in_array($section['type'] ?? '', ['intro', 'paragraph', 'closing'], true)
                            ? $section['type']
                            : 'paragraph',
                        'text' => trim((string) $section['text']),
                    ];
                })
                ->values()
                ->all();

            if ($references === [] || $sections === []) {
                return null;
            }

            return [
                'references' => $references,
                'answer_sections' => $sections,
            ];
        } catch (\Throwable $exception) {
            Log::warning('OpenAI advice request exception', [
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    protected function systemPrompt(string $bibleVersion): string
    {
        return <<<PROMPT
You are a Catholic spiritual advisor for Praise U Lord. Answer the user's question with pastoral guidance rooted in Scripture and Church teaching.

Rules:
- Respond ONLY with valid JSON matching this shape:
  {"references":["Book Chapter:Verse"],"answer_sections":[{"type":"intro|paragraph|closing","text":"..."}]}
- Provide 3 to 6 real Bible references in standard form (example: "Romans 1:26-27", "John 3:16", "Psalm 23:1-4").
- Do NOT quote Scripture verbatim in answer_sections; the application will insert verified passage text from the {$bibleVersion} Bible database.
- Do NOT include a scripture section; only intro, paragraph, and closing sections.
- Be compassionate, truthful, and faithful to Catholic teaching.
- If the question is unclear, still choose the most relevant biblical themes and references.
- Keep each section concise (1 to 3 sentences).
PROMPT;
    }

    /**
     * @param  array<int, array<string, string>>  $sections
     * @return array<int, array<string, string>>
     */
    protected function normalizeSections(array $sections): array
    {
        $normalized = collect($sections)
            ->reject(fn (array $section) => ($section['type'] ?? '') === 'scripture')
            ->values()
            ->all();

        if ($normalized === []) {
            return SpiritualAdviceFormatter::sectionsFromReferences([], []);
        }

        if (! collect($normalized)->contains(fn (array $section) => ($section['type'] ?? '') === 'intro')) {
            array_unshift($normalized, [
                'type' => 'intro',
                'text' => 'Thank you for your question. Based on Scripture, here is guidance that may help.',
            ]);
        }

        if (! collect($normalized)->contains(fn (array $section) => ($section['type'] ?? '') === 'closing')) {
            $normalized[] = [
                'type' => 'closing',
                'text' => 'May you find peace and clarity in God\'s loving presence.',
            ];
        }

        return $normalized;
    }
}
