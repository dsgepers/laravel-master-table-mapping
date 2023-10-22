<?php

namespace Schepeis\Mapping\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase;
use Schepeis\Mapping\Mapper;
use Schepeis\Mapping\Models\Mapping;
use Schepeis\Mapping\Providers\MasterTableMappingServiceProvider;
use Workbench\App\Models\Make;
use Workbench\Database\Seeders\DatabaseSeeder;
use function Orchestra\Testbench\workbench_path;

class MappingModelTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();
        $this->migrateDb();
    }

    protected function getPackageProviders($app): array
    {
        return [
            MasterTableMappingServiceProvider::class,
        ];
    }

    public function testMapperModel()
    {
        $mapping = new Mapping();

        $collection = $mapping->mappable;

        $this->assertNull($collection);

        $mapper = $this->app->make(Mapper::class);
        $result = $mapper->map(Make::class, "Volvo");
        $this->assertNotNull($result);

        $mapping = Mapping::first();
        $object = $mapping->mappable;

        $this->assertInstanceOf(Make::class, $object);
    }


    private function migrateDb() {
        $this->artisan('vendor:publish', [
            '--tag' => 'master-table-mapping',
            '--force' => true]);


        $this->artisan('migrate:refresh');

        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(workbench_path('database/migrations'));
        $this->artisan('db:seed', ['--class' => DatabaseSeeder::class]);
    }
}
