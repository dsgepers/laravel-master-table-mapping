<?php

namespace Workbench\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Workbench\App\Models\Make;
use function Orchestra\Testbench\workbench_path;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Make::truncate();
        $dataFile = fopen(workbench_path("database/data/makes.csv"), "r");
        while (($data = fgetcsv($dataFile, 2000, ",")) !== FALSE) {
            Make::create([
                "make" => $data['0']
            ]);
        }

        fclose($dataFile);
    }
}
