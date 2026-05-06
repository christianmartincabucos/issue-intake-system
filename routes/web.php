<?php

use App\Http\Controllers\IssueController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect('/issues'));

Route::get('/issues', [IssueController::class, 'webIndex']);
Route::get('/issues/create', [IssueController::class, 'webCreate']);
Route::post('/issues', [IssueController::class, 'webStore']);
Route::get('/issues/{id}', [IssueController::class, 'webShow']);
Route::get('/issues/{id}/edit', [IssueController::class, 'webEdit']);
Route::put('/issues/{id}', [IssueController::class, 'webUpdate']);
Route::patch('/issues/{id}/status', [IssueController::class, 'webUpdateStatus']);
Route::delete('/issues/{id}', [IssueController::class, 'webDestroy']);
