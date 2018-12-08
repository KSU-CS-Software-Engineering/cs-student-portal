<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixMySqlErrorsInStrictMode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->text('description')->change();
        });

        Schema::table('colleges', function (Blueprint $table) {
            $table->string('college_abbr')->nullable()->change();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->text('requisites')->change();
        });

        Schema::table('advisors', function (Blueprint $table) {
            $table->text('notes')->nullable()->change();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('wildcat_id', 10)->nullable()->change();
            $table->string('ksis_id', 10)->nullable()->change();
        });

        Schema::table('degreeprograms', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });

        Schema::table('degreerequirements', function (Blueprint $table) {
            $table->string('course_name')->nullable()->change();
        });

        Schema::table('planrequirements', function (Blueprint $table) {
            $table->string('course_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('planrequirements')->whereNull('course_name')->update(['course_name' => '']);
        Schema::table('planrequirements', function (Blueprint $table) {
            $table->string('course_name')->nullable(false)->change();
        });

        DB::table('degreerequirements')->whereNull('course_name')->update(['course_name' => '']);
        Schema::table('degreerequirements', function (Blueprint $table) {
            $table->string('course_name')->nullable(false)->change();
        });

        DB::table('degreeprograms')->whereNull('description')->update(['description' => '']);
        Schema::table('degreeprograms', function (Blueprint $table) {
            $table->longText('description')->nullable(false)->change();
        });

        DB::table('students')->whereNull('ksis_id')->update(['ksis_id' => '']);
        DB::table('students')->whereNull('wildcat_id')->update(['wildcat_id' => '']);
        Schema::table('students', function (Blueprint $table) {
            $table->string('ksis_id', 10)->nullable(false)->change();
            $table->string('wildcat_id', 10)->nullable(false)->change();
        });

        DB::table('advisors')->whereNull('notes')->update(['notes' => '']);
        Schema::table('advisors', function (Blueprint $table) {
            $table->longText('notes')->nullable(false)->change();
        });

        DB::table('courses')->update(['requisites' => DB::raw('SUBSTRING(`requisites`, 1, 255)')]);
        Schema::table('courses', function (Blueprint $table) {
            $table->string('requisites')->change();
        });

        DB::table('colleges')->whereNull('college_abbr')->update(['college_abbr' => '']);
        Schema::table('colleges', function (Blueprint $table) {
            $table->string('college_abbr')->nullable(false)->change();
        });

        DB::table('areas')->update(['description' => DB::raw('SUBSTRING(`description`, 1, 255)')]);
        Schema::table('areas', function (Blueprint $table) {
            $table->string('description')->change();
        });
    }
}
