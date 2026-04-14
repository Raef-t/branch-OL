<?php

namespace Modules\Shared\Observers;

use Illuminate\Support\Facades\Log;
use Modules\Shared\Traits\HandlesFileCleanup;

/**
 * Class GenericFileObserver
 *
 * Observes Eloquent model events to automatically handle file cleanup for models
 * that have file path attributes defined in the configuration.
 *
 * - On update: Deletes old files if file fields are changed.
 * - On force delete: Deletes all files associated with the model.
 * - On delete: Also deletes all files (for soft deletes, if needed).
 *
 * @see \App\Traits\HandlesFileCleanup
 */
class GenericFileObserver
{
    use HandlesFileCleanup;

    /**
     * Handle the "updated" event for the model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function updated($model): void
    {
      
        $this->deleteOldFiles($model);
    }

    /**
     * Handle the "forceDeleted" event for the model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function forceDeleted($model): void
    {
        $this->deleteAllFiles($model);
    }

    /**
     * Handle the "deleted" event for the model (for soft deletes).
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function deleted($model): void
    {


        $this->deleteAllFiles($model);
    }
}
