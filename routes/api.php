<?php

use App\Http\Controllers\IssueController;
use Illuminate\Support\Facades\Route;

Route::get('/issues', [IssueController::class, 'index']);
Route::post('/issues', [IssueController::class, 'store']);
Route::get('/issues/{id}', [IssueController::class, 'show']);
Route::put('/issues/{id}', [IssueController::class, 'update']);
Route::delete('/issues/{id}', [IssueController::class, 'destroy']);
