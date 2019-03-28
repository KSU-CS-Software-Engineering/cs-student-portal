<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        //Here call fill CLassScheduleTable
        //$this->call(ScheduledClassTableSeeder::class);
        //$this->call(MeetingSeeder::class);


        $this->call(UserTableSeeder::class);
        $this->call(AreasTableSeeder::class);
        $this->call(PrefixesTableSeeder::class);
        $this->call(CoursesTableSeeder::class);
        $this->call(PrerequisitesTableSeeder::class);
        $this->call(SchedulerSeeder::class);
        $this->call(StudentSeeder::class);

        $this->call(DegreeProgramSeeder::class);
        $this->call(PlansSeeder::class);
        $this->call(SectionsSeeder::class);
        Model::reguard();
    }
}
