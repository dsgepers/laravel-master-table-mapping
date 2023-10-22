<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Schepeis\Mapping\Traits\Mappable;

class Make extends Model
{
    use Mappable;

    protected $fillable = [
        'make',
    ];

    public function getMappableFieldName(): string {
        return 'make';
    }
}
