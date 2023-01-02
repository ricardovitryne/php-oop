<?php
require_once __DIR__.'/Http/Controllers/Auth/AuthControlller.php';
require_once __DIR__.'/Http/Controllers/UserController.php';
require_once __DIR__.'/Http/Controllers/UserDrinkController.php';
require_once __DIR__.'/Http/Route.php';
require_once __DIR__.'/Http/Middlewares/AuthAutenticate.php';
require_once __DIR__.'/Http/Response/Response.php';

//LOGIN ROUTE
Route::post('/login', [AuthControlller::class, 'login']);
Route::post('/logoff', [AuthControlller::class, 'logoff'], [AuthAutenticate::class]);

//USER ROUTES
Route::post('/users/', [UserController::class, 'store']);
Route::get('/users/', [UserController::class, 'index'], [AuthAutenticate::class]);
Route::get('/users/{iduser}', [UserController::class, 'show'], [AuthAutenticate::class]);
Route::put('/users/{iduser}', [UserController::class, 'update'], [AuthAutenticate::class]);
Route::delete('/users/{iduser}', [UserController::class, 'delete'], [AuthAutenticate::class]);

Response::toJSON(['ERROR' => 'Route not found!'], 404);