<?php

namespace Modules\Batches\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class VisibleBatchScope implements Scope
{
    /**
     * تطبيق السكوب: إخفاء الدورات المخفية افتراضياً.
     * يمكن تجاوزه باستخدام: Batch::withoutGlobalScope(VisibleBatchScope::class)
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where($model->getTable() . '.is_hidden', false);
    }
}
