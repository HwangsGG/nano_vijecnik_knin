<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('city_id')->unsigned()->index()->nullable();
            $table->longText('data')->nullable();
            $table->string('telephone')->nullable();
            $table->bigInteger('account_type_id')->unsigned()->index()->nullable();
            $table->timestamps();
            $table->tinyInteger('active')->nullable();

            $table->foreign('city_id')
                ->references('id')
                ->on('cities')
                ->onDelete('cascade');

            $table->foreign('account_type_id')
                ->references('id')
                ->on('account_types')
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
        Schema::dropIfExists('accounts');
    }
}
