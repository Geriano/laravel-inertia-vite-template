<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temporary_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')
                    ->unique();
            $table->unsignedBigInteger('token_id')
                    ->unique();
            $table->timestamps();

            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnDelete();

            $table->foreign('token_id')
                    ->references('id')
                    ->on('personal_access_tokens')
                    ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temporary_tokens');
    }
};
