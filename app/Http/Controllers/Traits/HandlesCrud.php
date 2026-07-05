<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait HandlesCrud
{
    abstract protected function modelClass(): string;

    abstract protected function storeRules(): array;

    abstract protected function updateRules(int $id): array;

    protected function searchableFields(): array
    {
        return [];
    }

    protected function sortableFields(): array
    {
        return ['id', 'created_at', 'updated_at'];
    }

    protected function filterableFields(): array
    {
        return [];
    }

    protected function defaultRelations(): array
    {
        return [];
    }

    protected function importUniqueKeys(): array
    {
        return ['id'];
    }

    protected function usesSoftDeletes(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($this->modelClass()), true);
    }

    public function index(Request $request): JsonResponse
    {
        $query = $this->buildIndexQuery($request);

        $perPage = min(max((int) $request->input('per_page', 15), 1), 500);

        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $data = Validator::make($request->all(), $this->storeRules())->validate();

        $model = $this->modelClass()::create($data);
        $model->load($this->defaultRelations());

        return response()->json($model, 201);
    }

    public function show(int $id): JsonResponse
    {
        $model = $this->findModel($id);

        return response()->json($model);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $model = $this->findModel($id, withTrashed: true);

        $data = Validator::make($request->all(), $this->updateRules($id))->validate();

        $model->update($data);
        $model->load($this->defaultRelations());

        return response()->json($model);
    }

    public function destroy(int $id): JsonResponse
    {
        $model = $this->findModel($id);

        if ($this->usesSoftDeletes()) {
            $model->delete();
        } else {
            $model->forceDelete();
        }

        return response()->json(['message' => 'Deleted successfully.']);
    }

    public function restore(int $id): JsonResponse
    {
        if (! $this->usesSoftDeletes()) {
            return response()->json(['message' => 'This resource does not support restore.'], 422);
        }

        $model = $this->modelClass()::onlyTrashed()->findOrFail($id);
        $model->restore();
        $model->load($this->defaultRelations());

        return response()->json($model);
    }

    public function forceDelete(int $id): JsonResponse
    {
        if (! $this->usesSoftDeletes()) {
            return response()->json(['message' => 'This resource does not support force delete.'], 422);
        }

        $model = $this->modelClass()::withTrashed()->findOrFail($id);
        $model->forceDelete();

        return response()->json(['message' => 'Permanently deleted.']);
    }

    public function export(Request $request): StreamedResponse
    {
        $records = $this->buildIndexQuery($request)->get();

        $filename = class_basename($this->modelClass()).'-export-'.now()->format('Y-m-d-His').'.json';

        return response()->streamDownload(function () use ($records) {
            echo json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:json,txt'],
        ]);

        $contents = file_get_contents($request->file('file')->getRealPath());
        $payload = json_decode($contents, true);

        if (! is_array($payload)) {
            return response()->json(['message' => 'Invalid JSON file.'], 422);
        }

        $items = array_is_list($payload) ? $payload : ($payload['data'] ?? []);

        if (! is_array($items)) {
            return response()->json(['message' => 'JSON must be an array of records.'], 422);
        }

        $imported = 0;

        DB::transaction(function () use ($items, &$imported) {
            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }

                unset($item['created_at'], $item['updated_at'], $item['deleted_at']);

                $modelClass = $this->modelClass();
                $keys = $this->importUniqueKeys();

                if (! empty($keys) && $this->hasImportKeys($item, $keys)) {
                    $query = $modelClass::query();

                    if ($this->usesSoftDeletes()) {
                        $query->withTrashed();
                    }

                    foreach ($keys as $key) {
                        $query->where($key, $item[$key]);
                    }

                    $existing = $query->first();

                    if ($existing) {
                        $existing->update($item);
                        $imported++;

                        continue;
                    }
                }

                $modelClass::create($item);
                $imported++;
            }
        });

        return response()->json([
            'message' => 'Import completed.',
            'imported' => $imported,
        ]);
    }

    protected function buildIndexQuery(Request $request)
    {
        $query = $this->modelClass()::query()->with($this->defaultRelations());

        if ($this->usesSoftDeletes()) {
            if ($request->boolean('only_trashed')) {
                $query->onlyTrashed();
            } elseif ($request->boolean('with_trashed')) {
                $query->withTrashed();
            }
        }

        if ($search = $request->input('search')) {
            $fields = $this->searchableFields();

            if ($fields !== []) {
                $query->where(function ($builder) use ($fields, $search) {
                    foreach ($fields as $field) {
                        $builder->orWhere($field, 'like', '%'.$search.'%');
                    }
                });
            }
        }

        foreach ($this->filterableFields() as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->input($field));
            }
        }

        $sort = $request->input('sort', 'id');
        $direction = strtolower((string) $request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        if (in_array($sort, $this->sortableFields(), true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('id', 'desc');
        }

        return $query;
    }

    protected function findModel(int $id, bool $withTrashed = false): Model
    {
        $query = $this->modelClass()::query()->with($this->defaultRelations());

        if ($withTrashed && $this->usesSoftDeletes()) {
            $query->withTrashed();
        }

        return $query->findOrFail($id);
    }

    protected function hasImportKeys(array $item, array $keys): bool
    {
        foreach ($keys as $key) {
            if (! array_key_exists($key, $item) || $item[$key] === null || $item[$key] === '') {
                return false;
            }
        }

        return true;
    }
}
