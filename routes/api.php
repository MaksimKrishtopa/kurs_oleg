<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('registration', 'Api\UserController@registration')->middleware('guestUser'); //Рега
Route::post('login', 'Api\UserController@login')->middleware('guestUser'); //Логин

Route::middleware('auth:sanctum')->group(function () {
    Route::get('logout', 'Api\UserController@logout'); //Выход
    Route::get('profile', 'Api\UserController@profile')->middleware('user'); //Профиль
    Route::get('profile/records', 'Api\UserController@records')->middleware('user'); //Записи к врачам определенного юзера
    Route::delete('profile/records/delete/{id}', 'Api\UserController@destroy')->middleware('user'); //Удаление записи на приём к врачу юзером
    Route::post('specializations/create', 'Api\SpecializationController@store')->middleware('admin'); //Создание специализации
    Route::get('doctors', 'Api\DoctorController@index')->middleware('admin'); //Список врачей для админа
    Route::get('doctors/create', 'Api\DoctorController@showSpecializations')->middleware('admin'); //Вывод специализаций на странице создания врача
    Route::post('doctors/create', 'Api\DoctorController@store')->middleware('admin'); //Создание врача
    Route::get('doctors/change/{id}', 'Api\DoctorController@showDoctor')->middleware('admin'); //Вывод данных в инпуты изменяемого врача
    Route::put('doctors/change/{id}', 'Api\DoctorController@update')->middleware('admin'); //Изменение данных врача
    Route::get('graph/create', 'Api\GraphController@index')->middleware('admin'); //Вывод врачей на странице создания расписания
    Route::post('graph/create', 'Api\GraphController@store')->middleware('admin'); //Создание расписания
    Route::get('record/create', 'Api\RecordController@index')->middleware('user'); //Функционал по селектам при выборе данных для записи на приём
    Route::post('record/create', 'Api\RecordController@store')->middleware('user'); //Запись к врачу
});
