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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')
                    ->nullable()
                    ->default(null);
            $table->string('name');
            $table->string('icon')
                    ->default('circle');
            $table->string('route_or_url')
                    ->nullable()
                    ->default('#');
            $table->unsignedTinyInteger('position');
            $table->boolean('enable')->default(true);
            $table->boolean('deleteable')->default(true);
            $table->longText('actives')
                    ->nullable()
                    ->default(null);
            $table->timestamps();

            $table->foreign('parent_id')
                    ->references('id')
                    ->on('menus')
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
        Schema::dropIfExists('menus');
    }
};
