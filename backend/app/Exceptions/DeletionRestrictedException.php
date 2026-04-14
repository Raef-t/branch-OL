<?php

namespace App\Exceptions;

use RuntimeException;

class DeletionRestrictedException extends RuntimeException
{
    /**
     * @param  array<int, string>  $relations
     */
    public function __construct(
        protected string $resource,
        protected array $relations = []
    ) {
        $relationsText = empty($relations)
            ? 'ارتباطات موجودة'
            : implode('، ', $relations);

        parent::__construct(
            "لا يمكن حذف {$resource} لوجود ارتباطات: {$relationsText}"
        );
    }

    public function resource(): string
    {
        return $this->resource;
    }

    /**
     * @return array<int, string>
     */
    public function relations(): array
    {
        return $this->relations;
    }
}

