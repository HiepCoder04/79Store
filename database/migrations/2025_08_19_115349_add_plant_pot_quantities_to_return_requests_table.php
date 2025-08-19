<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->integer('plant_quantity')->default(0)->after('quantity')->comment('Số lượng cây muốn trả');
            $table->integer('pot_quantity')->default(0)->after('plant_quantity')->comment('Số lượng chậu muốn trả');
        });
    }

    public function down()
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->dropColumn(['plant_quantity', 'pot_quantity']);
        });
    }
};