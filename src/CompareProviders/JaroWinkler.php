<?php

namespace Schepeis\Mapping\CompareProviders;

use Atomescrochus\StringSimilarities\Compare;
use Illuminate\Database\Eloquent\Model;
use Schepeis\Mapping\Models\Mapping;

class JaroWinkler extends CompareProvider
{
    private Compare $service;

    public function __construct(Compare $service)
    {
        $this->service = $service;
    }

    public function map(string $type, string $input): ?Model
    {
        $objects = app($type)->all();

        $topScorer = collect($objects)->map(fn (Model $object) => [
            'score' => $this->service->jaroWinkler($input, $object->getAttribute($object->getMappableFieldName())),
            'object' => $object,
        ])->sortByDesc('score')->first() ?? null;


        if (($topScorer['object'] ?? null)) {
            $topScorer['object']->mappings()->save(tap(new Mapping(), function (Mapping $mapping) use ($input, $topScorer) {
                $mapping->fill([
                    'input' => $input,
                    'confirmed' => false,
                    'provider' => SimilarText::class,
                    'score' => $topScorer['score'] ?? null,
                ]);
            }));
            if (isset($this->config['min-score']) === false || ($topScorer['score'] >= $this->config['min-score'])) {
                return $topScorer['object'];
            }
        }
        return null;
    }
}
