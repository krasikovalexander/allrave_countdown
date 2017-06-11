<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEventAddStyle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('main_bg_color')->nullable();
            $table->string('main_bg_image')->nullable();
            $table->string('area_bg_color')->nullable();
            $table->string('area_bg_image')->nullable();
            $table->string('area_text_color')->nullable();
            $table->string('area_timer_color')->nullable();
            $table->string('area_arrived_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('main_bg_color');
            $table->dropColumn('main_bg_image');
            $table->dropColumn('area_bg_color');
            $table->dropColumn('area_bg_image');
            $table->dropColumn('area_text_color');
            $table->dropColumn('area_timer_color');
            $table->dropColumn('area_arrived_color');
        });
    }
}
