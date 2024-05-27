<?php

use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/tasks',[\App\Http\Controllers\TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks',[\App\Http\Controllers\TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{task}',[\App\Http\Controllers\TaskController::class, 'update'])->name('tasks.update');
    Route::put('/tasks/{task}/status',[\App\Http\Controllers\TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::delete('/tasks/{task}',[\App\Http\Controllers\TaskController::class, 'destroy'])->name('tasks.destroy');
});
