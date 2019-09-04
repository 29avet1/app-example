<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebhookEndpointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webhook_endpoints', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->uuid('uuid');
            $table->unsignedInteger('team_id');
            $table->string('url')->unique();
            $table->string('secret_key', 350);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('uuid');
        });

        Schema::table('webhook_endpoints', function (Blueprint $table) {

            $table->foreign('team_id')
                ->references('id')
                ->on('teams')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('webhook_endpoints', function (Blueprint $table) {
            $table->dropForeign('webhook_endpoints_team_id_foreign');
        });

        Schema::dropIfExists('webhook_endpoints');
    }
}
