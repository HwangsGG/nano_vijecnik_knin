<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouncilVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('council_votes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('council_session_id')->unsigned()->index()->nullable();
            $table->bigInteger('council_session_item_id')->unsigned()->index()->nullable();
            $table->bigInteger('council_session_item_type_id')->unsigned()->index()->nullable();
            $table->bigInteger('user_id')->unsigned()->index()->nullable();
            $table->bigInteger('vote_type_id')->unsigned()->index()->nullable();
            $table->timestamps();

            $table->foreign('council_session_id')
                ->references('id')
                ->on('council_sessions')
                ->onDelete('cascade');

            $table->foreign('council_session_item_id')
                ->references('id')
                ->on('council_session_items')
                ->onDelete('cascade');

            $table->foreign('council_session_item_type_id')
                ->references('id')
                ->on('council_session_item_types')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('vote_type_id')
                ->references('id')
                ->on('vote_types')
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
        Schema::dropIfExists('council_votes');
    }
}
