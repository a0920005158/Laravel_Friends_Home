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

Route::post('/apix/{api}', 'entryController@excute');
Route::post('/bgx/{api}', 'bgEntryController@excute');
Route::get('/Line_Loginx', 'Line_LoginController@Run');
Route::post('/echo_bot', 'echo_botController@Run');

Route::post('/fastloginx', 'fastloginController@Run');
Route::post('/actionInvite', 'actionInviteController@Run');
Route::get('/lineCallback', 'lineCallbackController@Run');
Route::post('/test', 'testController@Run');
Route::post('/loginx', 'loginController@Run');