<?php

namespace App\Models\Concerns;

use App\Exceptions\DeletionRestrictedException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

trait RestrictDeletion
{
    /**
     * Boot the trait and register the deleting event.
     */
    public static function bootRestrictDeletion(): void
    {
        static::deleting(function (Model $model): void {
            $model->guardDeletionRestrictions();
        });
    }

    /**
     * Check if deletion is restricted and throw an exception if it is.
     * 
     * @throws DeletionRestrictedException
     */
    protected function guardDeletionRestrictions(): void
    {
        $blocked = $this->getDeletionRestrictions();

        if (! empty($blocked)) {
            throw new DeletionRestrictedException(
                $this->getDeletionRestrictionResource(),
                $blocked
            );
        }
    }

    /**
     * Get the list of labels for relations that are blocking deletion.
     * 
     * @return array<int, string>
     */
    public function getDeletionRestrictions(): array
    {
        $blocked = [];

        foreach ($this->getDeletionRestrictedRelations() as $relation => $label) {
            // Check if the relation method exists in the model
            if (! method_exists($this, $relation)) {
                continue;
            }

            try {
                // Use exists() to check for related records efficiently
                if ($this->{$relation}()->exists()) {
                    $blocked[] = $label;
                }
            } catch (\Exception $e) {
                // Log and skip if there's an issue with the relation definition
                Log::warning("Error checking restriction for relation '{$relation}' on model " . get_class($this) . " [ID: {$this->id}]: " . $e->getMessage());
                continue;
            }
        }

        return $blocked;
    }

    /**
     * Get the list of restricted relations and their labels.
     * 
     * @return array<string, string>
     */
    public function getDeletionRestrictedRelations(): array
    {
        return property_exists($this, 'deletionRestrictedRelations')
            ? (array) $this->deletionRestrictedRelations
            : [];
    }

    /**
     * Get a human-readable name for the resource being deleted.
     * 
     * @return string
     */
    protected function getDeletionRestrictionResource(): string
    {
        return property_exists($this, 'deletionRestrictionResource')
            ? (string) $this->deletionRestrictionResource
            : 'هذا السجل';
    }
}
