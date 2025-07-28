<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
{
    Schema::table('profiles', function (Blueprint $table) {
        $table->unsignedBigInteger('address_1')->nullable();
        $table->unsignedBigInteger('address_2')->nullable();

        $table->foreign('address_1')->references('id')->on('addresses')->onDelete('set null');
        $table->foreign('address_2')->references('id')->on('addresses')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('profiles', function (Blueprint $table) {
        $table->dropForeign(['address_1']);
        $table->dropForeign(['address_2']);
        $table->dropColumn(['address_1', 'address_2']);
    });
}

};
