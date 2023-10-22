<?php

namespace Schepeis\Mapping\CompareProviders;

use Illuminate\Database\Eloquent\Model;

/**
 * @codeCoverageIgnore
 */
class Gpt35 extends CompareProvider
{


    public function map(string $type, string $input): ?Model
    {
        return null;
    }
}
