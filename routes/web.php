<?php

use App\Http\Controllers\Web\CatalogController;
use App\Http\Controllers\Web\DashboardWebController;
use App\Http\Controllers\Web\MaintenanceWebController;
use App\Http\Controllers\Web\VehicleWebController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardWebController::class, 'index'])->name('dashboard');

Route::prefix('vehicles')->name('vehicles.')->group(function () {
    Route::get('/',                    [VehicleWebController::class, 'index'])->name('index');
    Route::get('/create',              [VehicleWebController::class, 'create'])->name('create');
    Route::post('/',                   [VehicleWebController::class, 'store'])->name('store');
    Route::get('/check-patente',       [VehicleWebController::class, 'checkPatente'])->name('checkPatente');
    Route::get('/{vehicle}',           [VehicleWebController::class, 'show'])->name('show');
    Route::get('/{vehicle}/edit',      [VehicleWebController::class, 'edit'])->name('edit');
    Route::put('/{vehicle}',           [VehicleWebController::class, 'update'])->name('update');
    Route::delete('/{vehicle}',        [VehicleWebController::class, 'destroy'])->name('destroy');
});

Route::prefix('maintenance')->name('maintenance.')->group(function () {
    Route::get('/', [MaintenanceWebController::class, 'index'])->name('index');
});

Route::prefix('config')->name('config.')->group(function () {
    Route::get('/{catalog}',         [CatalogController::class, 'index'])->name('index');
    Route::post('/{catalog}',        [CatalogController::class, 'store'])->name('store');
    Route::put('/{catalog}/{id}',    [CatalogController::class, 'update'])->name('update');
    Route::delete('/{catalog}/{id}', [CatalogController::class, 'destroy'])->name('destroy');
});

Route::get('/config', fn() => redirect()->route('config.index', 'colores'))->name('config');
