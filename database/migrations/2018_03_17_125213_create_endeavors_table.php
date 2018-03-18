<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEndeavorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('endeavors', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('creator_id')->unsigned();
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer('mentor_id')->nullable()->unsigned();
            $table->foreign('mentor_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('title');
            $table->text('objective');
            $table->integer('stage')->default(1);
            $table->integer('duration');
            $table->integer('points')->default(0);
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->boolean('is_finished')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('endeavors');
    }
}
