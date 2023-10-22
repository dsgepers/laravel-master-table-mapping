<?php
declare(strict_types=1);

namespace Schepeis\Mapping\Providers;

use Atomescrochus\StringSimilarities\Compare;
use Illuminate\Support\ServiceProvider;
use Schepeis\Mapping\Mapper;

class MasterTableMappingServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(Mapper::class, function($app) {
            /* @var $mapper Mapper */
            $mapper = $this->app->make(Mapper::class);
            $mapper->configure(...config('schepeis-mapping.providers.similarText.callable'));
            return $mapper;
        });
        $this->app->bind('schepeis-mapper', function($app) {
            return app(Mapper::class);
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publish();
        }
    }

    private function publish(): void
    {

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'schepeis-mapping-migrations');

        $this->publishes([
            __DIR__.'/../config/schepeis-mapping.php' => config_path('schepeis-mapping.php'),
        ], 'schepeis-mapping-config');
    }
}
