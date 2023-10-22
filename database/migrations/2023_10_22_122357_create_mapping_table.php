<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_table_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('input');
            $table->morphs('master_table');
            $table->boolean('confirmed')->default(false);
            $table->float('score')->nullable();
            $table->string('provider')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_table_mappings');
    }
};
