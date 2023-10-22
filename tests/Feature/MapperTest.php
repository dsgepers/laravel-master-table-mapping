<?php

namespace Schepeis\Mapping\Tests\Feature;

use Atomescrochus\StringSimilarities\Compare;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Orchestra\Testbench\TestCase;
use Schepeis\Mapping\CompareProviders\JaroWinkler;
use Schepeis\Mapping\CompareProviders\Levenshtein;
use Schepeis\Mapping\CompareProviders\SimilarText;
use Schepeis\Mapping\CompareProviders\SmithWatermanGotoh;
use Schepeis\Mapping\Mapper;
use Schepeis\Mapping\Providers\MasterTableMappingServiceProvider;
use Workbench\App\Models\Make;
use Workbench\Database\Seeders\DatabaseSeeder;
use function Orchestra\Testbench\workbench_path;

class MapperTest extends TestCase
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

    public function testNonSeededDB(): void
    {
        Make::truncate();

        $this->switchProvider('similarText');
        $mapper = $this->app->make(Mapper::class);
        $result = $mapper->map(Make::class, "Volvo");
        $this->assertNull($result);

        $this->switchProvider('jaroWinkler');
        $mapper = $this->app->make(Mapper::class);
        $result = $mapper->map(Make::class, "Volvo");
        $this->assertNull($result);

        $this->switchProvider('levenshtein');
        $mapper = $this->app->make(Mapper::class);
        $result = $mapper->map(Make::class, "Volvo");
        $this->assertNull($result);

        $this->switchProvider('smg');
        $mapper = $this->app->make(Mapper::class);
        $result = $mapper->map(Make::class, "Volvo");
        $this->assertNull($result);
    }


    public function testMinScoreNotSurpassed(): void
    {
        $this->switchProvider('similarText', [
            'master-table-mapping.providers.similarText.min-score' => 101,
        ]);
        $mapper = $this->app->make(Mapper::class);
        $result = $mapper->map(Make::class, "Volvo");
        $this->assertNull($result);
    }

    public function testNonMappableModel(): void
    {
        $this->switchProvider('similarText');

        $mapper = $this->app->make(Mapper::class);
        $result = $mapper->map(User::class, "Volvo");
        $this->assertNull($result);

        $mapper->setProvider($this->app->make(Levenshtein::class));
        $result = $mapper->map(User::class, "Volvo");
        $this->assertNull($result);
    }

    public function testSimilarTextMappableModel(): void
    {
        $this->switchProvider('similarText');

        /* @var $mapper Mapper */
        $mapper = $this->app->make(Mapper::class);

        $this->assertInstanceOf(SimilarText::class, $mapper->getProvider());

        $this->assertMakeByInput($mapper, "Mercedez-Benz", "Mercedes AMG");
        $this->assertMakeByInput($mapper, "Volvo", "Volvo");
        $this->assertMakeByInput($mapper, "Volvo", "Volvoo");
        $this->assertMakeByInput($mapper, "Volvo", "Volvo XC70");
        $this->assertMakeByInput($mapper, "Volkswagen", "Volks wagen");
        $this->assertMakeByInput($mapper, "Volkswagen", "Volkswaagen");
    }

    public function testJaroWinklerMappableModel(): void {
        $this->switchProvider('jaroWinkler');

        $mapper = $this->app->make(Mapper::class);

        $this->assertInstanceOf(JaroWinkler::class, $mapper->getProvider());

        $this->assertMakeByInput($mapper, "Volvo", "Volvo");
        $this->assertMakeByInput($mapper, "Volvo", "Volvo");
        $this->assertMakeByInput($mapper, "Volvo", "Volvoo");
        $this->assertMakeByInput($mapper, "Volvo", "Volvo XC70");
        $this->assertMakeByInput($mapper, "Volkswagen", "Volks wagen");
        $this->assertMakeByInput($mapper, "Volkswagen", "Volkswaagen");
    }
    public function testLevenshteinMappableModel(): void {
        $this->switchProvider('levenshtein');

        $mapper = $this->app->make(Mapper::class);

        $this->assertInstanceOf(Levenshtein::class, $mapper->getProvider());

        $this->assertMakeByInput($mapper, "Mercedez-Benz", "Mercedes AMG");
        $this->assertMakeByInput($mapper, "Volvo", "Volvo");
        $this->assertMakeByInput($mapper, "Volvo", "Volvoo");
        $this->assertMakeByInput($mapper, "Volvo", "Volvo XC70");
        $this->assertMakeByInput($mapper, "Volkswagen", "Volks wagen");
        $this->assertMakeByInput($mapper, "Volkswagen", "Volkswaagen");
    }
    public function testSMGMappableModel(): void {
        $this->switchProvider('smg');

        $mapper = $this->app->make(Mapper::class);

        $this->assertInstanceOf(SmithWatermanGotoh::class, $mapper->getProvider());

        $this->assertMakeByInput($mapper, "Mercedez-Benz", "Mercedes AMG");
        $this->assertMakeByInput($mapper, "Volvo", "Volvo");
        $this->assertMakeByInput($mapper, "Volvo", "Volvoo");
        $this->assertMakeByInput($mapper, "Volvo", "Volvo XC70");
        $this->assertMakeByInput($mapper, "Volkswagen", "Volks wagen");
        $this->assertMakeByInput($mapper, "Volkswagen", "Volkswaagen");
    }

    private function assertMakeByInput($mapper, $expected, $input) {

        $result = $mapper->map(Make::class, $input);
        $expected === null ? $this->assertNull($result) : $this->assertNotNull($result);
        if ($expected !== null) {
            $this->assertEquals($expected, $result->make);
        }
    }

    private function switchProvider($provider, $config = []) {

        foreach ($config as $key => $value) {
            config()->set($key, $value);
        }
        config()->set('master-table-mapping.default', $provider);

        (new MasterTableMappingServiceProvider($this->app))->register();
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
