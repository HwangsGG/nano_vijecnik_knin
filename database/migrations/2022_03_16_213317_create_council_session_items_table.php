<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouncilSessionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('council_session_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('council_session_id')->unsigned()->index()->nullable();
            $table->bigInteger('council_session_item_type_id')->unsigned()->index()->nullable();
            $table->string('name');
            $table->longText('data')->nullable();
            $table->tinyInteger('locked')->nullable();
            $table->timestamps();
            $table->tinyInteger('active')->nullable();
            $table->integer('item_number')->default(0);

            $table->foreign('council_session_id')
                ->references('id')
                ->on('council_sessions')
                ->onDelete('cascade');

            $table->foreign('council_session_item_type_id')
                ->references('id')
                ->on('council_session_item_types')
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
        Schema::dropIfExists('council_session_items');
    }
}
