<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->integer('type_id')->comment('Tip - 0 kvorum, 1- otvoreno');
            $table->bigInteger('account_id')->unsigned()->index()->nullable();
            $table->bigInteger('council_session_id')->unsigned()->index()->nullable();
            $table->bigInteger('council_session_item_id')->unsigned()->index()->nullable();
            $table->integer('user_id');
            $table->integer('status');
            $table->timestamps();

            $table->foreign('council_session_id')
                ->references('id')
                ->on('council_sessions')
                ->onDelete('cascade');

            $table->foreign('council_session_item_id')
                ->references('id')
                ->on('council_session_items')
                ->onDelete('cascade');

            $table->foreign('account_id')
                ->references('id')
                ->on('accounts')
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
        Schema::dropIfExists('notifications');
    }
}
