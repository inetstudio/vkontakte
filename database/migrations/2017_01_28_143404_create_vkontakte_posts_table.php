<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVkontaktePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vkontakte_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('post_id')->index();
            $table->string('from_id')->index();
            $table->string('owner_id')->default('')->index();
            $table->string('post_source')->default('');
            $table->string('post_type')->default('')->index();
            $table->text('text');
            $table->integer('comments')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('reposts')->default(0);
            $table->integer('views')->default(0);
            $table->timestamp('date')->nullable();
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
        Schema::drop('vkontakte_posts');
    }
}
