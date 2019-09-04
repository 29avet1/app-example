<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->unique();
            $table->unsignedInteger('team_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('contact_id');
            $table->string('invoice_number')->unique();
            $table->string('status')->default('draft');
            $table->string('currency', 3);
            $table->string('receipt_pdf')->nullable();
            $table->decimal('shipping_cost')->nullable();
            $table->decimal('refunded_amount')->nullable();
            $table->text('message')->nullable();
            $table->boolean('shipping')->default(false);
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('uuid');
            $table->index('status');
            $table->index('invoice_number');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreign('team_id')
                ->references('id')
                ->on('teams')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        //no need to add foreign keys, as we must save invoice data even if team has been deleted
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign('invoices_team_id_foreign');
        });

        Schema::dropIfExists('invoices');
    }
}
