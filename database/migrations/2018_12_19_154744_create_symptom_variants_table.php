<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSymptomVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('symptom_variants', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('symptom_id')->nullable();
            $table->string('name');
            $table->timestamps();

            $table->foreign('symptom_id')->references('id')->on('symptoms')->onDelete('cascade');

            $table->index(['name']);
        });

        DB::statement('alter table symptom_variants add column variant tsvector;');
        DB::statement('update symptom_variants set variant = to_tsvector(\'pg_catalog.simple\', \'name\');');
        DB::statement('create index symptom_variants_variant_index on symptom_variants using gin(variant);');
        DB::statement('create trigger ts_variant before insert or update on symptom_variants for each row execute procedure tsvector_update_trigger(\'variant\', \'pg_catalog.simple\', \'name\');');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('symptom_variants');
    }
}
