<?php

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
