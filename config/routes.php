<?php
declare(strict_types=1);

use App\Core\Router;

/**
 * Route table: HTTP method, URL pattern, 'Controller@action', requires login?
 */
return static function (Router $r): void {
    // Public
    $r->get('/',            'AuthController@home',      false);
    $r->get('/login',       'AuthController@showLogin', false);
    $r->post('/login',      'AuthController@login',     false);
    $r->get('/logout',      'AuthController@logout',    false);

    // Administrator area
    $r->get('/dashboard',   'DashboardController@index');

    $r->get('/students',         'StudentController@index');
    $r->get('/students/create',  'StudentController@create');
    $r->post('/students',        'StudentController@store');
    $r->get('/students/{id}/edit',      'StudentController@edit');
    $r->post('/students/{id}/update',   'StudentController@update');
    $r->post('/students/{id}/delete',   'StudentController@destroy');

    $r->get('/courses',     'CourseController@index');
    $r->post('/courses',    'CourseController@store');

    $r->get('/enrolments',  'EnrolmentController@index');
    $r->post('/enrolments', 'EnrolmentController@store');
    $r->post('/enrolments/delete', 'EnrolmentController@destroy');

    $r->get('/grades',      'GradeController@index');
    $r->post('/grades',     'GradeController@update');

    $r->get('/summary',          'SummaryController@index');
    $r->get('/transcript/{id}',  'TranscriptController@show');

    $r->get('/reports',                   'ReportController@index');
    $r->get('/reports/export/{type}',     'ReportController@export');

    $r->get('/settings',                    'SettingsController@index');
    $r->post('/settings/programmes',        'SettingsController@addProgramme');
    $r->post('/settings/programmes/delete', 'SettingsController@deleteProgramme');
};
