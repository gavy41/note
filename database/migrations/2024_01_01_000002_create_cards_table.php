<?php
// database/migrations/2024_01_01_000002_create_cards_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('所属用户');
            $table->enum('type', ['excerpt', 'inspiration', 'quote'])->comment('类型');
            $table->text('content')->comment('碎片内容');
            $table->string('source', 255)->nullable()->comment('来源书名');
            $table->string('author', 128)->nullable()->comment('作者');
            $table->string('color', 32)->default('#e8e2d8')->comment('卡片背景色');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'created_at']);
            $table->index('type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cards');
    }
}
