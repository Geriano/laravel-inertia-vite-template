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
            $table->string('route_or_url')->default('#');
            $table->string('icon')
                  ->default('circle');
            $table->boolean('enable')
                  ->default(true);
            $table->integer('position');
            $table->json('actives')
                  ->default('[]');
            $table->boolean('deleteable')
                  ->default(true);
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
