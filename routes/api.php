<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/login',[AuthController::class,'login']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


//Manager
Route::prefix('manager')->middleware(['auth:api','role:Manager'])->group(function (){
    Route::resource('tasks',\App\Http\Controllers\Manager\TaskController::class);
});


//User
Route::middleware(['auth:api','role:User'])->group(function (){
    Route::resource('tasks',\App\Http\Controllers\User\TaskController::class);
});

