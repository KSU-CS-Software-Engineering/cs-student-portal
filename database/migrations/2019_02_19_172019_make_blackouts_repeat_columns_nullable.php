<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeBlackoutsRepeatColumnsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blackouts', function (Blueprint $table) {
            $table->smallInteger('repeat_every')->nullable()->change();
            $table->smallInteger('repeat_detail')->nullable()->change();
            $table->dateTime('repeat_until')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blackouts', function (Blueprint $table) {
            $table->smallInteger('repeat_every')->nullable(false)->change();
            $table->smallInteger('repeat_detail')->nullable(false)->change();
            $table->dateTime('repeat_until')->nullable(false)->change();
        });
    }
}
