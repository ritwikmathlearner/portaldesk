<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::resource('tasks', 'TaskController');
Route::post('/tasks/{task}/allocate', 'TaskController@allocate')->name('tasks.allocate');
Route::post('/tasks/{task}/deallocate', 'TaskController@deallocate')->name('tasks.deallocate');
Route::post('/tasks/{task}/invite', 'TaskController@invite')->name('tasks.invite');
Route::post('/tasks/{task}/deinvite', 'TaskController@deinvite')->name('tasks.deinvite');
Route::post('/tasks/{task}/fileDownload', 'TaskController@fileDownload')->name('tasks.fileDownload');
Route::post('/tasks/{task}/solutionUpload', 'TaskController@solutionUpload')->name('tasks.solutionUpload');
Route::post('/tasks/{task}/solutionRemove', 'TaskController@solutionRemove')->name('tasks.solutionRemove');
Route::post('/tasks/{task}/requirementUpload', 'TaskController@requirementUpload')->name('tasks.requirementUpload');
Route::post('/tasks/{task}/changeStatus', 'TaskController@changeStatus')->name('tasks.changeStatus');
Route::post('/tasks/{task}/escalate', 'TaskController@escalate')->name('tasks.escalate');
Route::post('/tasks/{task}/fail', 'TaskController@fail')->name('tasks.fail');
Route::post('/tasks/{task}/message', 'TaskController@storeMessage')->name('tasks.message');
Route::resource('tags', 'TagController')->only(['store', 'destroy']);

