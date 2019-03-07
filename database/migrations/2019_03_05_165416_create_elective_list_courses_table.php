<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElectiveListCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('elective_list_courses', function (Blueprint $table) {
            $table->unsignedInteger('course_id');
            $table->unsignedInteger('elective_list_id');
            $table->foreign('course_id')->references('id')->on('courses');
            $table->foreign('elective_list_id')->references('id')->on('electivelists');
            $table->timestamps();
            $table->primary(array('course_id', 'elective_list_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('elective_list_courses');
    }
}
