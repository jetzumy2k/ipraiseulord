<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesCrud;
use App\Models\AiConversation;
use App\Services\BibleAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiAdviceController extends Controller
{
    use HandlesCrud;

    public function __construct(protected BibleAiService $bibleAi) {}

    public function ask(Request $request): JsonResponse
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:2000'],
            'visitor_id' => ['nullable', 'string', 'max:255'],
            'bible_version' => ['nullable', 'string', 'max:50'],
        ]);

        $result = $this->bibleAi->ask(
            question: $data['question'],
            visitorId: $data['visitor_id'] ?? null,
            userId: $request->user()?->id,
            bibleVersion: $data['bible_version'] ?? null,
        );

        $conversation = $result['conversation'];

        return response()->json([
            'question' => $conversation->question,
            'answer' => $conversation->answer,
            'answer_sections' => $result['answer_sections'],
            'references' => $conversation->bible_references,
            'conversation_id' => $conversation->id,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $conversation = AiConversation::query()->findOrFail($id);

        return response()->json([
            'question' => $conversation->question,
            'answer' => $conversation->answer,
            'answer_sections' => $this->bibleAi->answerSectionsFromAnswer($conversation->answer),
            'references' => $conversation->bible_references ?? [],
            'conversation_id' => $conversation->id,
            'bible_version' => $conversation->bible_version,
        ]);
    }

    public function searchVerses(Request $request): JsonResponse
    {
        $data = $request->validate([
            'query' => ['required', 'string', 'max:500'],
            'bible_version' => ['nullable', 'string', 'max:50'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $verses = $this->bibleAi->searchVerses(
            query: $data['query'],
            bibleVersion: $data['bible_version'] ?? null,
            limit: $data['limit'] ?? 10,
        );

        return response()->json($verses);
    }

    protected function modelClass(): string
    {
        return AiConversation::class;
    }

    protected function searchableFields(): array
    {
        return ['question', 'answer', 'visitor_id', 'bible_version'];
    }

    protected function filterableFields(): array
    {
        return ['user_id', 'bible_version'];
    }

    protected function storeRules(): array
    {
        return [
            'visitor_id' => ['nullable', 'string', 'max:255'],
            'user_id' => ['nullable', 'exists:users,id'],
            'question' => ['required', 'string'],
            'answer' => ['required', 'string'],
            'bible_references' => ['nullable', 'array'],
            'bible_version' => ['nullable', 'string', 'max:50'],
        ];
    }

    protected function updateRules(int $id): array
    {
        return [
            'visitor_id' => ['nullable', 'string', 'max:255'],
            'user_id' => ['nullable', 'exists:users,id'],
            'question' => ['sometimes', 'string'],
            'answer' => ['sometimes', 'string'],
            'bible_references' => ['nullable', 'array'],
            'bible_version' => ['nullable', 'string', 'max:50'],
        ];
    }
}
