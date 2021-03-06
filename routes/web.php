<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
 * Static routes for static pages
 * Tested in tests/RouteTest.php
 */
Route::get('/', 'RootRouteController@getIndex');
Route::get('/about', 'RootRouteController@getAbout');
Route::get('/help', 'RootRouteController@getHelp');
Route::get('/test', 'RootRouteController@getTest');


/*
 * Static route for images in storage
 * http://stackoverflow.com/questions/30191330/laravel-5-how-to-access-image-uploaded-in-storage-within-view
 */

Route::get('images/{filename}', 'RootRouteController@getImage');

/*
 * Routes for the CoursesController
 */
//Route::controller('courses', 'CoursesController');
Route::get('courses/', 'CoursesController@getIndex');
Route::get('courses/category/{category}', 'CoursesController@getCategory');
Route::get('courses/course/{slug}', 'CoursesController@getCourse');
Route::get('courses/id/{id}', 'CoursesController@getCourseById');
Route::get('courses/coursefeed', 'CoursesController@getCoursefeed');
Route::get('courses/prereqfeed/{id}', 'CoursesController@getPrereqs');

/*
 * Routes for the CompletedcoursesController
 */
Route::get('completedcourses/completedcoursefeed/{id}', 'CompletedcoursesController@getCompletedcoursefeed');

/*
 * Routes for Scheduler controller
 */
Route::get('scheduler/{plan}', 'SchedulerController@show');
Route::get('scheduler/{plan}/current-semester', 'SchedulerController@getCurrentSemesterId');
Route::get('scheduler/{plan}/get-semester', 'SchedulerController@getSemesters');
Route::get('scheduler/{semester}/sections', 'SchedulerController@getSemesterSections');

/*
 * Routes for the FlowchartsController
 */
//Route::controller('flowcharts', 'FlowchartsController');
Route::get('flowcharts/new/{id}', 'FlowchartsController@newFlowchart');
Route::post('flowcharts/new/{id}', 'FlowchartsController@saveNewFlowchart');
Route::get('flowcharts/{id?}', 'FlowchartsController@getIndex');
Route::get('flowcharts/view/{id}', 'FlowchartsController@getFlowchart');
Route::get('flowcharts/edit/{id}', 'FlowchartsController@editFlowchart');
Route::post('flowcharts/edit/{id}', 'FlowchartsController@saveFlowchart');
Route::post('flowcharts/delete', 'FlowchartsController@deleteFlowchart');
Route::post('flowcharts/reset', 'FlowchartsController@resetFlowchart');

Route::get('flowcharts/{plan}/semesters', 'FlowchartSemestersController@getSemesters');
Route::post('flowcharts/{plan}/semesters', 'FlowchartSemestersController@addSemester');
Route::patch('flowcharts/{plan}/semesters', 'FlowchartSemestersController@moveSemester');
Route::patch('flowcharts/{plan}/semesters/{semester}', 'FlowchartSemestersController@renameSemester');
Route::delete('flowcharts/{plan}/semesters/{semester}', 'FlowchartSemestersController@deleteSemester');
Route::post('flowcharts/{plan}/semesters/{semester}/set-summer', 'FlowchartSemestersController@setSummer');

Route::get('flowcharts/{plan}/courses', 'FlowchartRequirementsController@getCourses');
Route::post('flowcharts/{plan}/requirements', 'FlowchartRequirementsController@addRequirement');
Route::put('flowcharts/{plan}/requirements/{requirement}', 'FlowchartRequirementsController@updateRequirement');
Route::patch('flowcharts/{plan}/requirements/{requirement}', 'FlowchartRequirementsController@moveRequirement');
Route::delete('flowcharts/{plan}/requirements/{requirement}', 'FlowchartRequirementsController@deleteRequirement');
Route::get('flowcharts/{plan}/errors', 'FlowchartsController@errors');

/*
 * Routes for the AdvisingController
 */
//Route::controller('advising', 'AdvisingController');
Route::get('advising', 'AdvisingController@getIndex');
Route::get('advising/index/{id?}', 'AdvisingController@getIndex');
Route::get('advising/select/{dept?}', 'AdvisingController@getSelect');
Route::get('advising/meetingfeed', 'AdvisingController@getMeetingfeed');
Route::get('advising/blackoutfeed', 'AdvisingController@getBlackoutfeed');
Route::get('advising/blackout', 'AdvisingController@getBlackout');
Route::get('advising/meeting', 'AdvisingController@getMeeting');
Route::get('advising/conflicts', 'AdvisingController@getConflicts');
Route::post('advising/createmeeting', 'AdvisingController@postCreatemeeting');
Route::post('advising/deletemeeting', 'AdvisingController@postDeletemeeting');
Route::post('advising/createblackout', 'AdvisingController@postCreateblackout');
Route::post('advising/createblackoutevent', 'AdvisingController@postCreateblackoutevent');
Route::post('advising/deleteblackout', 'AdvisingController@postDeleteblackout');
Route::post('advising/deleteblackoutevent', 'AdvisingController@postDeleteblackoutevent');
Route::post('advising/resolveconflict', 'AdvisingController@postResolveconflict');

/*
 * Routes for the ProfilesController
 */
//Route::controller('profile', 'ProfilesController');
Route::get('profile/', 'ProfilesController@getIndex');
Route::get('profile/pic/{id?}', 'ProfilesController@getPic');
Route::get('profile/studentfeed', 'ProfilesController@getStudentfeed');
Route::post('profile/update', 'ProfilesController@postUpdate');
Route::post('profile/newstudent', 'ProfilesController@postNewstudent');

/*
 * Routes for the GroupsessionController
 */
//Route::controller('groupsession', 'GroupsessionController');
Route::get('groupsession/', 'GroupsessionController@getIndex');
Route::get('groupsession/list', 'GroupsessionController@getList');
Route::get('groupsession/queue', 'GroupsessionController@getQueue');
Route::post('groupsession/register', 'GroupsessionController@postRegister');
Route::post('groupsession/take', 'GroupsessionController@postTake');
Route::post('groupsession/put', 'GroupsessionController@postPut');
Route::post('groupsession/done', 'GroupsessionController@postDone');
Route::post('groupsession/delete', 'GroupsessionController@postDelete');
Route::get('groupsession/enable', 'GroupsessionController@getEnable');
Route::post('groupsession/disable', 'GroupsessionController@postDisable');

/*
 * Routes for ElectivelistsController
 */
Route::get('electivelists/electivelistfeed', 'ElectivelistsController@getElectivelistfeed');

/*
 * Routes for Authentication
 */
Route::get('auth/login', 'Auth\AuthController@CASLogin');
Route::get('auth/logout', 'Auth\AuthController@Logout');
Route::get('auth/caslogout', 'Auth\AuthController@CASLogout');
Route::get('auth/force', 'Auth\AuthController@ForceLogin');

Route::post('editable/save/{id?}', 'EditableController@postSave');
