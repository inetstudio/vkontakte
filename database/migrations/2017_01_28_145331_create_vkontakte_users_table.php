<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVkontakteUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vkontakte_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id')->index();

            $table->string('first_name')->default('');
            $table->string('last_name')->default('');
            $table->string('nickname')->default('');
            $table->string('screen_name')->default('');

            $table->tinyInteger('has_photo')->default(0);
            $table->string('photo_id')->default('');
            $table->string('photo_50', 1000)->default('');
            $table->string('photo_100', 1000)->default('');
            $table->string('photo_200', 1000)->default('');
            $table->string('photo_200_orig', 1000)->default('');
            $table->string('photo_400_orig', 1000)->default('');
            $table->string('photo_max', 1000)->default('');
            $table->string('photo_max_orig', 1000)->default('');

            $table->integer('followers_count')->default(0);
            $table->integer('common_count')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('vkontakte_users');
    }
}
