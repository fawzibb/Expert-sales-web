<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('item_order', function (Blueprint $table) {
        $table->integer('quantity')->default(1); // لإضافة كمية الأصناف
        $table->decimal('price', 8, 2); // لإضافة السعر
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('item_order', function (Blueprint $table) {
        $table->dropColumn(['quantity', 'price']);
    });
}
};
