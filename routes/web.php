<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');
Route::middleware(['auth', 'verified'])->group(function () {
    // Ruta principal para crear un nuevo relevamiento
    Volt::route('/relevamiento/nuevo', 'relevamiento-wizard')
        ->name('relevamiento.nuevo');

    // (Opcional) Ruta para editar un relevamiento existente
    Volt::route('/relevamiento/{id}/editar', 'relevamiento-wizard')
        ->name('relevamiento.editar');

    // Listado de todos los equipos
    Volt::route('/activos', 'activos-index')
        ->name('activos.index');

    // Listado de relevamientos por oficina
    Volt::route('/relevamientos', 'relevamientos-index')
        ->name('relevamientos.index');

    // Listado de impresoras
    Volt::route('/impresoras', 'impresoras-index')
        ->name('impresoras.index');

    // Listado de modems
    Volt::route('/modems', 'modems-index')
        ->name('modems.index');
});
require __DIR__ . '/auth.php';
