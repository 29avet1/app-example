<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSelectedWebhooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selected_webhooks', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedInteger('team_id');
            $table->unsignedSmallInteger('endpoint_id');
            $table->string('type');

            $table->index('type');
            $table->unique(['type', 'endpoint_id']);
        });

        Schema::table('selected_webhooks', function (Blueprint $table) {
            $table->foreign('team_id')
                ->references('id')
                ->on('teams')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('endpoint_id')
                ->references('id')
                ->on('webhook_endpoints')
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
        Schema::table('selected_webhooks', function (Blueprint $table) {
            $table->dropForeign('selected_webhooks_team_id_foreign');
            $table->dropForeign('selected_webhooks_endpoint_id_foreign');
        });

        Schema::dropIfExists('selected_webhooks');
    }
}
