<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateVkontaktePostsTables.
 */
class CreateVkontaktePostsTables extends Migration
{
    /**
     * Run the migrations.

     */
    public function up()
    {
        Schema::create('vkontakte_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('post_id')->index();
            $table->string('user_id')->index();
            $table->json('additional_info');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.

     */
    public function down()
    {
        Schema::drop('vkontakte_posts');
    }
}
