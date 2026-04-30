<?php

use Illuminate\Support\Facades\Route;

// ========== API para Mapa Interactivo IN-C01 ==========
Route::get('/lotes/geojson', [App\Http\Controllers\Api\LoteGeoController::class, 'geojson'])->name('api.lotes.geojson');
Route::get('/lotes/{lote}', [App\Http\Controllers\Api\LoteGeoController::class, 'show'])->name('api.lotes.show');

// ========== API para registro de acceso (IN-L06) ==========
Route::post('/folio/acceso', [App\Http\Controllers\Api\FolioAccesoController::class, 'registrar'])->name('api.folio.acceso');