<?php

namespace Modules\Audits\Services;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Modules\Audits\Models\Audit;

class AuditValuePresenter
{
    /**
     * Fields that should never be shown in clear text inside audit responses.
     *
     * @var array<int, string>
     */
    private const MASKED_FIELDS = [
        'email',
        'national_id',
        'password',
        'phone',
        'remember_token',
    ];

    /**
     * Format audit values for frontend consumption.
     *
     * This reuses the original model accessors/casts so encrypted attributes
     * are returned in the same readable form used elsewhere in the app.
     */
    public function present(Audit $audit, mixed $values): mixed
    {
        if (! is_array($values)) {
            $decoded = json_decode((string) $values, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return $values;
            }

            $values = $decoded;
        }

        $model = $this->resolveAuditableModel($audit);
        $presented = [];

        foreach ($values as $attribute => $value) {
            if ($this->shouldSkipAttribute($attribute)) {
                continue;
            }

            $presented[$attribute] = $this->maskSensitiveValue(
                $attribute,
                $this->transformAttributeValue($model, $attribute, $value)
            );
        }

        return $presented;
    }

    private function resolveAuditableModel(Audit $audit): ?Model
    {
        if ($audit->relationLoaded('auditable') && $audit->auditable instanceof Model) {
            return $audit->auditable;
        }

        $modelClass = $this->resolveAuditableClass($audit->auditable_type);

        if (! $modelClass || ! is_subclass_of($modelClass, Model::class)) {
            return null;
        }

        return new $modelClass();
    }

    private function resolveAuditableClass(?string $auditableType): ?string
    {
        if (! is_string($auditableType) || $auditableType === '') {
            return null;
        }

        if (class_exists($auditableType)) {
            return $auditableType;
        }

        return Relation::getMorphedModel($auditableType);
    }

    private function shouldSkipAttribute(string $attribute): bool
    {
        return str_ends_with($attribute, '_hash');
    }

    private function transformAttributeValue(?Model $model, string $attribute, mixed $value): mixed
    {
        if (! $model || is_array($value) || is_object($value)) {
            return $value;
        }

        try {
            $model->setRawAttributes([$attribute => $value], true);
            $transformedValue = $model->getAttribute($attribute);

            if ($transformedValue instanceof DateTimeInterface) {
                return $model->serializeDate($transformedValue);
            }

            return $transformedValue;
        } catch (\Throwable) {
            return $value;
        }
    }

    private function maskSensitiveValue(string $attribute, mixed $value): mixed
    {
        if (! in_array($attribute, self::MASKED_FIELDS, true)) {
            return $value;
        }

        if ($value === null || $value === '') {
            return $value;
        }

        return '***';
    }
}
