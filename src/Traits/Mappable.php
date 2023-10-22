<?php

namespace Schepeis\Mapping\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Schepeis\Mapping\Models\Mapping;

trait Mappable
{
    public function getMappableFieldName() {
        return 'name';
    }

    /**
     * Get all of the post's comments.
     */
    public function mappings(): MorphMany
    {
        return $this->morphMany(Mapping::class, 'master_table');
    }
}
