<?php

namespace Schepeis\Mapping\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase;
use Schepeis\Mapping\Providers\MasterTableMappingServiceProvider;
use Schepeis\Mapping\Traits\Mappable;
use Workbench\App\Models\Make;
use Workbench\Database\Seeders\DatabaseSeeder;
use function Orchestra\Testbench\workbench_path;

class MapperTraitTest extends TestCase
{
    use Mappable;
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

    public function testMapperTrait()
    {
        $this->assertEquals('name', $this->getMappableFieldName());
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
