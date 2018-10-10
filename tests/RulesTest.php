<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Student;
use App\Models\Advisor;
use App\Models\Department;



class RulesTest extends TestCase {
    use DatabaseTransactions;
}
