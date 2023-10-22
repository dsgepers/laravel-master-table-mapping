<?php

namespace Schepeis\Mapping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Mapping extends Model
{

    protected $fillable = [
        'confirmed',
        'input',
        'provider',
        'score'
    ];
    /**
     * Get the parent mappable model.
     */
    public function mappable(): MorphTo
    {
        return $this->morphTo('master_table');
    }
}
