<?php

use App\Http\Controllers\Usuario\UsuarioDashboardController;
use App\Http\Controllers\Usuario\UsuarioTicketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Usuario'])->group(function () {
    Route::controller(UsuarioDashboardController::class)->group(function () {
        Route::get('/user-dashboard', 'index')->name('user-dashboard');
    });

    Route::controller(UsuarioTicketController::class)->group(function () {
        Route::get('/user-ticket', 'index')->name('user-ticket');
        Route::get('/user-tickets', 'traer');
        Route::post('/user-tickets', 'store');
        Route::put('/user-tickets/{user-ticket}', 'update');
        Route::delete('/user-tickets/{user-ticket}/eliminar', 'eliminar');
    });
});