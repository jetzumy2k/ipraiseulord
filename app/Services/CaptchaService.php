<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CaptchaService
{
    private const CACHE_PREFIX = 'form_captcha:';

    private const TTL_MINUTES = 10;

    /**
     * @var list<string>
     */
    private const CONTEXTS = ['contact', 'login'];

    /**
     * @return array{id: string, type: string, question: string}
     */
    public function generate(string $context = 'default'): array
    {
        $challenge = $this->randomChallenge();
        $id = (string) Str::uuid();

        Cache::put(
            self::CACHE_PREFIX.$id,
            [
                'answers' => array_map(
                    fn (string $answer) => $this->normalizeAnswer($answer),
                    $challenge['answers'],
                ),
                'context' => $context,
            ],
            now()->addMinutes(self::TTL_MINUTES),
        );

        return [
            'id' => $id,
            'type' => $challenge['type'],
            'question' => $challenge['question'],
        ];
    }

    public function verify(string $id, string $answer, string $context): bool
    {
        $payload = Cache::pull(self::CACHE_PREFIX.$id);

        if (! is_array($payload)) {
            return false;
        }

        if (($payload['context'] ?? null) !== $context) {
            return false;
        }

        return in_array($this->normalizeAnswer($answer), $payload['answers'] ?? [], true);
    }

    /**
     * @throws ValidationException
     */
    public function validateOrFail(string $id, string $answer, string $context): void
    {
        if (! in_array($context, self::CONTEXTS, true)) {
            throw ValidationException::withMessages([
                'captcha_answer' => ['Invalid captcha context.'],
            ]);
        }

        if (! $this->verify($id, $answer, $context)) {
            throw ValidationException::withMessages([
                'captcha_answer' => ['The captcha answer is incorrect or has expired. Please try again.'],
            ]);
        }
    }

    /**
     * @return array{type: string, question: string, answers: list<string>}
     */
    protected function randomChallenge(): array
    {
        $generators = [
            fn () => $this->mathChallenge(),
            fn () => $this->spellingChallenge(),
            fn () => $this->blankChallenge(),
        ];

        return $generators[array_rand($generators)]();
    }

    /**
     * @return array{type: string, question: string, answers: list<string>}
     */
    protected function mathChallenge(): array
    {
        $operations = ['+', '-', 'x'];
        $operation = $operations[array_rand($operations)];

        if ($operation === '+') {
            $a = random_int(2, 19);
            $b = random_int(2, 19);

            return [
                'type' => 'math',
                'question' => "What is {$a} + {$b}?",
                'answers' => [(string) ($a + $b)],
            ];
        }

        if ($operation === '-') {
            $a = random_int(10, 30);
            $b = random_int(2, $a - 1);

            return [
                'type' => 'math',
                'question' => "What is {$a} - {$b}?",
                'answers' => [(string) ($a - $b)],
            ];
        }

        $a = random_int(2, 9);
        $b = random_int(2, 9);

        return [
            'type' => 'math',
            'question' => "What is {$a} x {$b}?",
            'answers' => [(string) ($a * $b)],
        ];
    }

    /**
     * @return array{type: string, question: string, answers: list<string>}
     */
    protected function spellingChallenge(): array
    {
        $numbers = [
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
        ];

        $number = array_rand($numbers);

        return [
            'type' => 'spelling',
            'question' => "Spell the number {$number} in English (one word).",
            'answers' => [$numbers[$number]],
        ];
    }

    /**
     * @return array{type: string, question: string, answers: list<string>}
     */
    protected function blankChallenge(): array
    {
        $prompts = [
            ['question' => 'Fill in the blank: The first book of the Bible is _____.', 'answers' => ['genesis']],
            ['question' => 'Fill in the blank: Jesus rose on the _____ day.', 'answers' => ['third', '3rd']],
            ['question' => 'Fill in the blank: Mary is the Mother of _____.', 'answers' => ['god', 'jesus']],
            ['question' => 'Fill in the blank: The color of grass is usually _____.', 'answers' => ['green']],
            ['question' => 'Fill in the blank: There are _____ days in a week.', 'answers' => ['seven', '7']],
            ['question' => 'Fill in the blank: The opposite of night is _____.', 'answers' => ['day']],
            ['question' => 'Fill in the blank: Water freezes at _____ degrees Celsius.', 'answers' => ['zero', '0']],
            ['question' => 'Fill in the blank: A triangle has _____ sides.', 'answers' => ['three', '3']],
        ];

        $prompt = $prompts[array_rand($prompts)];

        return [
            'type' => 'blank',
            'question' => $prompt['question'],
            'answers' => $prompt['answers'],
        ];
    }

    protected function normalizeAnswer(string $answer): string
    {
        $normalized = Str::lower(trim($answer));
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;
        $normalized = str_replace(['.', ',', '!', '?'], '', $normalized);

        return $normalized;
    }
}
