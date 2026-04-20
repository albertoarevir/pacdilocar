<?php

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MaintenanceController;
use App\Http\Controllers\Api\VehicleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Control de Flota Vehicular
|--------------------------------------------------------------------------
|
| Prefijo base: /api/v1
|
*/

Route::prefix('v1')->group(function () {

    // ── Dashboard ─────────────────────────────────────────────────────────
    Route::prefix('dashboard')->controller(DashboardController::class)->group(function () {
        Route::get('summary',      'summary');
        Route::get('fleet-status', 'fleetStatus');
        Route::post('refresh',     'refreshAllSummaries');
    });

    // ── Vehículos ─────────────────────────────────────────────────────────
    Route::apiResource('vehicles', VehicleController::class);
    Route::get('vehicles/{vehicle}/summary', [VehicleController::class, 'operationalSummary']);

    // ── Mantenimiento / Taller ────────────────────────────────────────────
    Route::apiResource('maintenance', MaintenanceController::class);

    // ── Catálogos (solo lectura) ──────────────────────────────────────────
    Route::get('vehicle-types',          fn () => \App\Models\VehicleType::orderBy('code')->get());
    Route::get('maintenance-categories', fn () => \App\Models\MaintenanceCategory::orderBy('name')->get());
    Route::get('workshops',              fn () => \App\Models\Workshop::active()->orderBy('name')->get());
    Route::get('zones',                  fn () => \App\Models\Zone::orderBy('name')->get());
    Route::get('prefectures',            fn () => \App\Models\Prefecture::with('zone:id,name')->orderBy('name')->get());
    Route::get('units',                  fn () => \App\Models\Unit::with('prefecture:id,name')->orderBy('name')->get());
    Route::get('fuel-types',             fn () => \App\Models\FuelType::orderBy('name')->get());
    Route::get('colors',                 fn () => \App\Models\Color::orderBy('name')->get());
    Route::get('funding-origins',        fn () => \App\Models\FundingOrigin::orderBy('name')->get());
    Route::get('municipalities',         fn () => \App\Models\Municipality::with('province:id,name')->orderBy('name')->get());
});
