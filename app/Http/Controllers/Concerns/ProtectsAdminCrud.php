<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use InvalidArgumentException;

trait ProtectsAdminCrud
{
    /**
     * Create endpoints must never receive a row id (avoids overwrite via autofill / stale forms).
     */
    protected function forgetRequestRecordIds(Request $request, array $extraKeys = []): void
    {
        foreach (array_merge(['id', '_id'], $extraKeys) as $key) {
            $request->request->remove($key);
        }
    }

    /**
     * Resolve a single model for update/destroy. Never falls back to another row.
     *
     * @template T of Model
     * @param  class-string<T>  $modelClass
     * @return T
     */
    protected function findAdminRecord(string $modelClass, mixed $id): Model
    {
        if (! is_subclass_of($modelClass, Model::class)) {
            throw new InvalidArgumentException($modelClass.' must be an Eloquent model.');
        }

        $recordId = (int) $id;
        if ($recordId < 1) {
            abort(404);
        }

        return $modelClass::query()->findOrFail($recordId);
    }

    /**
     * Ensure a create path never accidentally persists an existing model instance.
     */
    protected function assertCreatingNew(Model $model): void
    {
        if ($model->exists) {
            abort(409, 'Create blocked: the form tried to update an existing record.');
        }
    }

    /**
     * Ensure an update path never switches to a different row before save.
     */
    protected function assertSameRecord(Model $model, int $expectedId): void
    {
        if ((int) $model->id !== $expectedId) {
            abort(409, 'Update blocked to protect existing records.');
        }
    }

    /**
     * Generate a unique slug for any model with a slug column.
     *
     * @param  class-string<Model>  $modelClass
     */
    protected function uniqueModelSlug(string $modelClass, string $title, ?int $ignoreId = null, string $fallback = 'item'): string
    {
        if (! is_subclass_of($modelClass, Model::class)) {
            throw new InvalidArgumentException($modelClass.' must be an Eloquent model.');
        }

        $base = Str::slug($title);
        if ($base === '') {
            $base = $fallback;
        }

        $slug = $base;
        $suffix = 1;

        $query = in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($modelClass), true)
            ? $modelClass::withTrashed()
            : $modelClass::query();

        while (
            (clone $query)
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
