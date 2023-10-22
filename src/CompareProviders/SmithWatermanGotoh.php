<?php
declare(strict_types=1);

namespace Schepeis\Mapping\CompareProviders;

use Atomescrochus\StringSimilarities\Compare;
use Illuminate\Database\Eloquent\Model;
use Schepeis\Mapping\Models\Mapping;

class SmithWatermanGotoh extends CompareProvider
{
    private Compare $service;

    public function __construct(Compare $service)
    {
        $this->service = $service;
    }

    public function map(string $type, string $input): ?Model
    {
        $objects = app($type)->all();

//        dd(collect($objects)->map(fn (Model $object) => [
//            'score' => $this->service->smg($input, $object->getAttribute($object->getMappableFieldName())),
//            'object' => $object,
//        ])->sortByDesc('score')->map(fn ($res) => [$res['score'], $res['object']->make]));
        $topScorer = collect($objects)->map(fn (Model $object) => [
            'score' => $this->service->smg($input, $object->getAttribute($object->getMappableFieldName())),
            'object' => $object,
        ])->sortByDesc('score')->first() ?? null;

        if (($topScorer['object'] ?? null)) {
            $topScorer['object']->mappings()->save(tap(new Mapping(), function (Mapping $mapping) use ($input, $topScorer) {
                $mapping->fill([
                    'input' => $input,
                    'confirmed' => false,
                    'provider' => SmithWatermanGotoh::class,
                    'score' => $topScorer['score'] ?? null,
                ]);
            }));
            return $topScorer['object'];
        }
        return null;
    }
}
