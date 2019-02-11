<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSectionsTablesForScraper extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('course_sections', 'sections');

        Schema::table('sections', function (Blueprint $table) {
            $table->string('course_number')->change();
            $table->string('section')->change();
            $table->string('type')->change();
            $table->string('units')->change();
            $table->string('days')->change();
            $table->string('hours')->nullable()->change();
            $table->string('facility')->nullable()->change();
            $table->string('instructor')->change();
            $table->dropForeign('course_sections_course_id_foreign');
            $table->unsignedInteger('course_id')->nullable()->change();
            $table->foreign('course_id')->references('id')->on('courses');
            $table->unique(['course_number', 'section']);
        });

        Schema::table('scheduled_classes', function (Blueprint $table) {
            $table->dropForeign('scheduled_classes_course_section_id_foreign');
            $table->renameColumn('course_section_id', 'section_id');
            $table->primary(['section_id', 'schedule_id']);
            $table->foreign('section_id')->references('id')->on('sections');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('sections', 'course_sections');

        Schema::table('scheduled_classes', function (Blueprint $table) {
            $table->dropForeign('scheduled_classes_section_id_foreign');
            $table->dropPrimary();
            $table->renameColumn('section_id', 'course_section_id');
            $table->foreign('course_section_id')->references('id')->on('course_sections');
        });

        Schema::table('course_sections', function (Blueprint $table) {
            $table->dropUnique('sections_course_number_section_unique');
            $table->dropForeign('sections_course_id_foreign');
        });

        Schema::table('course_sections', function (Blueprint $table) {
            $table->unsignedInteger('course_id')->nullable(false)->change();
            $table->foreign('course_id')->references('id')->on('courses');
            $table->string('instructor', 100)->change();
            $table->string('facility', 20)->nullable(false)->change();
            $table->string('hours', 30)->nullable(false)->change();
            $table->string('days', 10)->change();
            $table->smallInteger('units')->change();
            $table->string('type', 10)->change();
            $table->string('section', 10)->change();
            $table->string('course_number', 10)->change();
        });
    }
}
