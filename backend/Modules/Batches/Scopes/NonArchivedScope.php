<?php

namespace Modules\Batches\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class NonArchivedScope implements Scope
{
    /**
     * تطبيق السكوب: استبعاد الشعب المؤرشفة افتراضياً.
     * يمكن التجاوز باستخدام: Batch::withoutGlobalScope(NonArchivedScope::class)
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where($model->getTable() . '.is_archived', false);
    }
}
