<?php

namespace Schepeis\Mapping\CompareProviders;

use Illuminate\Database\Eloquent\Model;

abstract class CompareProvider
{
    protected array $config;

    abstract public function map(string $type, string $input): ?Model;

    public function setConfig($config) {
        $this->config = $config;
    }
}
