<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiseasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diseases', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['name']);
        });

        DB::statement('alter table diseases add column name_ts tsvector;');
        DB::statement('update diseases set name_ts = to_tsvector(\'pg_catalog.simple\', \'name\');');
        DB::statement('create index diseases_name_ts_index on diseases using gin(name_ts);');
        DB::statement('create trigger ts_disease before insert or update on diseases for each row execute procedure tsvector_update_trigger(\'name_ts\', \'pg_catalog.simple\', \'name\');');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diseases');
    }
}
