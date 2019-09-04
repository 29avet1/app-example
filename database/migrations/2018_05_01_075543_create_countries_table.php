<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('local_name')->nullable();
            $table->string('code', 2)->unique();
            $table->string('phone', 5);
            $table->string('language_code', 2);
            $table->string('language_name');
            $table->string('local_language_name');
            $table->boolean('postal_code_required');

            $table->index('code');
            $table->index('language_code');
            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
