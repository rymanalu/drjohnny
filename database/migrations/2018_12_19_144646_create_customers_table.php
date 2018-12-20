<?php

use App\Customer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('access_id');
            $table->string('username');
            $table->string('first_name');
            $table->string('last_name');
            $table->tinyInteger('gender')->default(Customer::GENDER_NOT_KNOWN);
            $table->string('avatar')->default('https://via.placeholder.com/150.png');
            $table->timestamps();

            $table->index(['access_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
