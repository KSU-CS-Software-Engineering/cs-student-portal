<?php
/*
 * Routes for the DashboardController
 */
//Route::controller('admin', 'DashboardController');
Route::get('', 'DashboardController@getIndex');

Route::get('students/{id?}', 'StudentsController@getStudents');
Route::get('newstudent', 'StudentsController@getNewstudent');
Route::post('students/{id?}', 'StudentsController@postStudents');
Route::post('newstudent', 'StudentsController@postNewstudent');
Route::post('deletestudent', 'StudentsController@postDeletestudent');
Route::post('forcedeletestudent', 'StudentsController@postForcedeletestudent');
Route::post('restorestudent', 'StudentsController@postRestorestudent');

Route::get('advisors/{id?}', 'AdvisorsController@getAdvisors');
Route::get('newadvisor', 'AdvisorsController@getNewadvisor');
Route::post('advisors/{id?}', 'AdvisorsController@postAdvisors');
Route::post('newadvisor', 'AdvisorsController@postNewadvisor');
Route::post('deleteadvisor', 'AdvisorsController@postDeleteadvisor');
Route::post('forcedeleteadvisor', 'AdvisorsController@postForcedeleteadvisor');
Route::post('restoreadvisor', 'AdvisorsController@postRestoreadvisor');

Route::get('departments/{id?}', 'DepartmentsController@getDepartments');
Route::get('newdepartment', 'DepartmentsController@getNewdepartment');
Route::post('departments/{id?}', 'DepartmentsController@postDepartments');
Route::post('newdepartment', 'DepartmentsController@postNewdepartment');
Route::post('deletedepartment', 'DepartmentsController@postDeletedepartment');
Route::post('restoredepartment', 'DepartmentsController@postRestoredepartment');
Route::post('forcedeletedepartment', 'DepartmentsController@postForcedeletedepartment');

Route::get('meetings/{id?}', 'MeetingsController@getMeetings');
Route::post('deletemeeting', 'MeetingsController@postDeletemeeting');
Route::post('forcedeletemeeting', 'MeetingsController@postForcedeletemeeting');

Route::get('blackouts/{id?}', 'BlackoutsController@getBlackouts');
Route::post('deleteblackout', 'BlackoutsController@postDeleteblackout');


Route::get('groupsessions/{id?}', 'GroupsessionsController@getGroupsessions');
Route::post('deletegroupsession', 'GroupsessionsController@postDeletegroupsession');


Route::get('settings', 'SettingsController@getSettings');
Route::post('newsetting', 'SettingsController@postNewsetting');
Route::post('savesetting', 'SettingsController@postSavesetting');

Route::get('degreeprograms', 'DegreeprogramsController@getDegreeprograms');
Route::get('degreeprograms/{id?}', 'DegreeprogramsController@getDegreeprogramDetail');
Route::get('degreeprograms/{id?}/edit', 'DegreeprogramsController@getDegreeprograms');
Route::get('newdegreeprogram', 'DegreeprogramsController@getNewdegreeprogram');
Route::post('degreeprograms/{id?}', 'DegreeprogramsController@postDegreeprograms');
Route::post('newdegreeprogram', 'DegreeprogramsController@postNewdegreeprogram');
Route::post('deletedegreeprogram', 'DegreeprogramsController@postDeletedegreeprogram');
Route::post('restoredegreeprogram', 'DegreeprogramsController@postRestoredegreeprogram');
Route::post('forcedeletedegreeprogram', 'DegreeprogramsController@postForcedeletedegreeprogram');

Route::post('newdegreerequirement/', 'DegreerequirementsController@postNewdegreerequirement');
Route::get('degreeprogramrequirements/{id?}', 'DegreerequirementsController@getDegreerequirementsForProgram');
Route::get('degreerequirement/{id?}', 'DegreerequirementsController@getDegreerequirement');
Route::post('degreerequirement/{id?}', 'DegreerequirementsController@postDegreerequirement');
Route::post('deletedegreerequirement', 'DegreerequirementsController@postDeletedegreerequirement');

Route::get('electivelists', 'ElectivelistsController@getElectivelists');
Route::get('electivelists/{id?}', 'ElectivelistsController@getElectivelistDetail');
Route::get('electivelists/{id?}/edit', 'ElectivelistsController@getElectivelists');
Route::get('newelectivelist', 'ElectivelistsController@getNewelectivelist');
Route::post('electivelists/{id?}', 'ElectivelistsController@postElectivelists');
Route::post('newelectivelist', 'ElectivelistsController@postNewelectivelist');
Route::post('deleteelectivelist', 'ElectivelistsController@postDeleteelectivelist');
Route::post('restoreelectivelist', 'ElectivelistsController@postRestoreelectivelist');
Route::post('forcedeleteelectivelist', 'ElectivelistsController@postForcedeleteelectivelist');

Route::get('electivelistcourses/{id?}', 'ElectivelistcoursesController@getElectivelistcoursesforList');
Route::post('newelectivelistcourse/', 'ElectivelistcoursesController@postNewelectivelistcourse');
Route::post('deleteelectivecourse', 'ElectivelistcoursesController@postDeleteelectivelistcourse');
Route::post('electivecourse/{id?}', 'ElectivelistcoursesController@postElectivelistcourse');
Route::get('electivecourse/{id?}', 'ElectivelistcoursesController@getElectivelistcourse');

Route::get('plans', 'PlansController@getPlans');
Route::get('plans/{id?}', 'PlansController@getPlanDetail');
Route::get('plans/{id?}/edit', 'PlansController@getPlans');
Route::get('newplan', 'PlansController@getNewplan');
Route::post('plans/{id?}', 'PlansController@postPlans');
Route::post('newplan', 'PlansController@postNewplan');
Route::post('deleteplan', 'PlansController@postDeleteplan');
Route::post('restoreplan', 'PlansController@postRestoreplan');
Route::post('forcedeleteplan', 'PlansController@postForcedeleteplan');
Route::post('populateplan', 'PlansController@postPopulateplan');

Route::post('newplanrequirement/', 'PlanrequirementsController@postNewplanrequirement');
Route::get('planrequirements/{id?}', 'PlanrequirementsController@getPlanrequirementsForPlan');
Route::get('planrequirement/{id?}', 'PlanrequirementsController@getPlanrequirement');
Route::post('planrequirement/{id?}', 'PlanrequirementsController@postPlanrequirement');
Route::post('deleteplanrequirement', 'PlanrequirementsController@postDeleteplanrequirement');

Route::get('plans/plansemesters/{id?}', 'PlansemestersController@getPlanSemestersForPlan');
Route::get('plans/plansemester/{id?}', 'PlansemestersController@getPlanSemester');
Route::get('plans/newplansemester/{id?}', 'PlansemestersController@getNewPlanSemester');
Route::post('plans/newplansemester/{id?}', 'PlansemestersController@postNewPlanSemester');
Route::post('plans/plansemester/{id?}', 'PlansemestersController@postPlanSemester');
Route::post('plans/deleteplansemester/{id?}', 'PlansemestersController@postDeletePlanSemester');


Route::get('completedcourses/{id?}', 'CompletedcoursesController@getCompletedcourses');
Route::get('newcompletedcourse', 'CompletedcoursesController@getNewcompletedcourse');
Route::post('completedcourses/{id?}', 'CompletedcoursesController@postCompletedcourses');
Route::post('newcompletedcourse', 'CompletedcoursesController@postNewcompletedcourse');
Route::post('deletecompletedcourse', 'CompletedcoursesController@postDeletecompletedcourse');
