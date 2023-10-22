<?php
declare(strict_types=1);

namespace Schepeis\Mapping\Providers;

use Atomescrochus\StringSimilarities\Compare;
use Illuminate\Support\ServiceProvider;
use Schepeis\Mapping\CompareProviders\CompareProvider;
use Schepeis\Mapping\Mapper;

class MasterTableMappingServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(CompareProvider::class, function($app) {
            $default = config('master-table-mapping.default');
            $handler = config("master-table-mapping.providers.{$default}.handler");

            /* @var CompareProvider $handlerObject */
            $handlerObject = $app->make($handler);
            $handlerObject->setConfig(config("master-table-mapping.providers.{$default}"));
            return $handlerObject;
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
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
        ], 'master-table-mapping');

        $this->publishes([
            __DIR__ . '/../../config/master-table-mapping.php' => config_path('master-table-mapping.php'),
        ], 'master-table-mapping');
    }
}
