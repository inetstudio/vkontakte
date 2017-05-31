<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVkontakteCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vkontakte_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('comment_id')->index();
            $table->string('post_id')->index();
            $table->string('from_id')->index();
            $table->text('text');
            $table->integer('likes')->default(0);
            $table->timestamp('date');
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
        Schema::drop('vkontakte_comments');
    }
}
