<?php
declare(strict_types=1);

namespace Schepeis\Mapping;

use Atomescrochus\StringSimilarities\Compare;
use Illuminate\Database\Eloquent\Model;
use Schepeis\Mapping\CompareProviders\CompareProvider;
use Schepeis\Mapping\Models\Mapping;
use Schepeis\Mapping\Traits\Mappable;

class Mapper
{
    private CompareProvider $provider;

    public function __construct(CompareProvider $provider)
    {
        $this->provider = $provider;
    }

    public function map($type, $input): ?Model
    {
        if ((class_uses($type)[Mappable::class] ?? null) === null) {
            return null;
        }


        if($exists = Mapping::where('master_table_type', $type)->where('input', $input)->first()) {
            return $exists->mappable;
        }

        $response = $this->provider->map($type, $input);


        return $response;
    }

    public function getProvider(): CompareProvider
    {
        return $this->provider;
    }

    public function setProvider(CompareProvider $provider): self
    {
        $this->provider = $provider;
        return $this;
    }
}
