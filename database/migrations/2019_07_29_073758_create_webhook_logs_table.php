<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebhookLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('team_id');
            $table->unsignedSmallInteger('endpoint_id');
            $table->unsignedSmallInteger('response_status_code');
            $table->string('type');
            $table->json('request_body');
            $table->text('response_body');
            $table->timestamps();

            $table->index('type');
        });

        Schema::table('webhook_logs', function (Blueprint $table) {
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
        Schema::table('webhook_logs', function (Blueprint $table) {
            $table->dropForeign('webhook_logs_team_id_foreign');
            $table->dropForeign('webhook_logs_endpoint_id_foreign');
        });

        Schema::dropIfExists('webhook_logs');
    }
}
