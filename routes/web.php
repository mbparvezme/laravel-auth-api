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

Route::any('/', function () {echo "<div style='text-align:center;user-select:none;padding-top:12%'>Access forbidden</div>";});
Route::any('/{a}', function () {echo "<div style='text-align:center;user-select:none;padding-top:12%'>Access forbidden</div>";});
Route::any('/{a}/{b}', function () {echo "<div style='text-align:center;user-select:none;padding-top:12%'>Access forbidden</div>";});
