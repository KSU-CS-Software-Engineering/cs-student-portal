<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleCoursesectionsScheduledcourseTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('semester_id');
            $table->timestamps();
            $table->foreign('semester_id')->references('id')->on('semesters');
        });

        Schema::create('course_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('course_number', 10);
            $table->string('section', 10);
            $table->string('type', 10);
            $table->smallInteger('units');
            $table->string('days', 10);
            $table->string('hours', 30);
            $table->string('facility', 20);
            $table->string('instructor', 100);
            $table->unsignedInteger('course_id');
            $table->foreign('course_id')->references('id')->on('courses');
        });

        Schema::create('scheduled_classes', function (Blueprint $table) {
            $table->unsignedInteger('course_section_id');
            $table->unsignedInteger('schedule_id');
            $table->foreign('course_section_id')->references('id')->on('course_sections');
            $table->foreign('schedule_id')->references('id')->on('schedules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scheduled_classes');
        Schema::dropIfExists('course_sections');
        Schema::dropIfExists('schedules');
    }
}
