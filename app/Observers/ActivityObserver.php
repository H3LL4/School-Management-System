<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

/**
 * Automatic audit trail: one observer, attached to many models in AppServiceProvider.
 */
class ActivityObserver
{
    public function created(Model $model): void
    {
        ActivityLog::record('create', 'Created ' . $this->label($model));
    }

    public function updated(Model $model): void
    {
        ActivityLog::record('update', 'Updated ' . $this->label($model));
    }

    public function deleted(Model $model): void
    {
        ActivityLog::record('delete', 'Deleted ' . $this->label($model));
    }

    private function label(Model $model): string
    {
        $name = $model->getAttribute('name')
            ?? $model->getAttribute('title')
            ?? $model->getAttribute('reference');

        return class_basename($model) . ' #' . $model->getKey() . ($name ? " ({$name})" : '');
    }
}
