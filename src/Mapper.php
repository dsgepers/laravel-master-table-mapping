<?php
declare(strict_types=1);

namespace Schepeis\Mapping;

use Atomescrochus\StringSimilarities\Compare;
use Illuminate\Database\Eloquent\Model;
use Schepeis\Mapping\Models\Mapping;
use Schepeis\Mapping\Traits\Mappable;

class Mapper
{
    private Compare $handler;
    private string $method;

    public function map($type, $input): ?Model
    {
        if ((class_uses($type)[Mappable::class] ?? null) === null) {
            return null;
        }

        if($exists = Mapping::where('master_table_type', $type)->where('input', $input)->first()) {
            return $exists->mappable;
        }


        $objects = app($type)->all();

        $topScorer = collect($objects)->map(fn (Model $object) => [
            'score' => call_user_func([$this->handler, $this->method], ...[$object->getAttribute($object->getMappableFieldName()), $input]),
            'object' => $object,
        ])->sortBy('score')->first() ?? null;

        if (($topScorer['object'] ?? null)) {
            $topScorer['object']->mappings()->save(tap(new Mapping(), function (Mapping $mapping) use ($input, $topScorer) {
                $mapping->fill([
                    'input' => $input,
                    'confirmed' => false,
                    'provider' => get_class($this->handler) . "::{$this->method}",
                    'score' => $topScorer['score'] ?? null,
                ]);
            }));
        }

        return $topScorer['object'];
    }

    public function configure(string $class, string $method): self {

        $this->handler = app($class);
        $this->method = $method;
        return $this;
    }
}
