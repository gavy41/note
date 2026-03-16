<?php
// database/migrations/2024_01_01_000001_create_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('openid', 64)->unique()->comment('微信 openid');
            $table->string('nickname', 64)->nullable()->comment('微信昵称');
            $table->string('avatar', 255)->nullable()->comment('头像 URL');
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();

            $table->index('openid');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
