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
    Schema::table('menus', function (Blueprint $table) {
      $table->after('route_or_url', function (Blueprint $table) {
        $table->string('counter_handler', 512)
              ->nullable()
              ->default(null);
      });
    });
  }
  
  /**
  * Reverse the migrations.
  *
  * @return void
  */
  public function down()
  {
    Schema::table('menus', function (Blueprint $table) {
      $table->dropColumn('counter_handler');
    });
  }
};
