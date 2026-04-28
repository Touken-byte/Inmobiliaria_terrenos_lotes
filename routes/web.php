<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VendedorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TerrenoController;
use App\Http\Controllers\DocumentoPropiedadController;
use App\Http\Controllers\SolicitudVisitaController;
use App\Http\Controllers\MinutaController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas Públicas
Route::get('/terrenos', [TerrenoController::class, 'index'])->name('terrenos.index');

// CATÁLOGO COMPRADOR (Marketplace)
Route::get('/catalogo', [TerrenoController::class, 'catalogo'])->name('catalogo.terrenos');
Route::get('/catalogo/{id}', [TerrenoController::class, 'detalle'])->name('catalogo.detalle');

// Autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas de Vendedor
Route::middleware(['auth', 'role:vendedor'])->prefix('vendedor')->name('vendedor.')->group(function () {
    Route::get('/dashboard', [VendedorController::class, 'dashboard'])->name('dashboard');
    Route::post('/subir-ci', [VendedorController::class, 'subirCI'])->name('subir_ci');
    Route::delete('/eliminar-ci', [VendedorController::class, 'eliminarCI'])->name('eliminar_ci'); // ✅ CORREGIDO
    Route::get('/mi-ci', [VendedorController::class, 'servirMiCI'])->name('mi_ci');
    Route::get('/historial', [VendedorController::class, 'historialPropio'])->name('historial');

    // Terrenos
    Route::delete('/terreno-imagen/{id}', [TerrenoController::class, 'eliminarImagen'])->name('terrenos.imagen.destroy');
    Route::get('/terrenos/crear', [TerrenoController::class, 'create'])->name('terrenos.create');
    Route::post('/terrenos', [TerrenoController::class, 'store'])->name('terrenos.store');
    Route::get('/mis-terrenos', [TerrenoController::class, 'misTerrenos'])->name('terrenos.mis');
    Route::get('/terrenos/editar/{id}', [TerrenoController::class, 'edit'])->name('terrenos.edit');
    Route::put('/terrenos/{id}', [TerrenoController::class, 'update'])->name('terrenos.update');

    // Documentos propiedad
    Route::get('/terrenos/{id}/documentos', [DocumentoPropiedadController::class, 'mostrarFormularioSubida'])->name('documentos.subir');
    Route::post('/terrenos/{id}/documentos', [DocumentoPropiedadController::class, 'subirDocumento'])->name('documentos.store');
    Route::delete('/terrenos/{id}/documentos', [DocumentoPropiedadController::class, 'eliminarDocumento'])->name('documentos.destroy');
    Route::get('/documentos-propiedad/{id}', [DocumentoPropiedadController::class, 'verDocumento'])->name('documentos.ver');

    // Control de lotes
    Route::get('/lotes', [VendedorController::class, 'controlLotes'])->name('lotes');
    Route::post('/lotes/{id}/estado', [VendedorController::class, 'updateLoteEstado'])->name('lotes.estado');

    // Comprobante IT
    Route::get('/comprobante-it', [\App\Http\Controllers\Vendedor\ComprobanteItController::class, 'index'])->name('comprobante_it');
    Route::post('/comprobante-it', [\App\Http\Controllers\Vendedor\ComprobanteItController::class, 'store'])->name('comprobante_it.store');
});

// Rutas compartidas (vendedor y admin) para solicitudes de visita
Route::middleware(['auth', 'role:vendedor,admin'])->prefix('vendedor')->name('vendedor.')->group(function () {
    Route::get('/solicitudes', [SolicitudVisitaController::class, 'index'])->name('solicitudes.index');
    Route::get('/solicitudes/calendario', [SolicitudVisitaController::class, 'calendario'])->name('solicitudes.calendario');
    Route::get('/solicitudes/crear', [SolicitudVisitaController::class, 'create'])->name('solicitudes.create');
    Route::post('/solicitudes', [SolicitudVisitaController::class, 'store'])->name('solicitudes.store');
    Route::get('/solicitudes/{id}', [SolicitudVisitaController::class, 'show'])->name('solicitudes.show');
    Route::post('/solicitudes/{id}/aprobar', [SolicitudVisitaController::class, 'aprobar'])->name('solicitudes.aprobar');
    Route::post('/solicitudes/{id}/rechazar', [SolicitudVisitaController::class, 'rechazar'])->name('solicitudes.rechazar');
    Route::post('/solicitudes/{id}/cancelar', [SolicitudVisitaController::class, 'cancelar'])->name('solicitudes.cancelar');
    Route::get('/api/solicitudes/eventos', [SolicitudVisitaController::class, 'eventos'])->name('solicitudes.eventos');
    Route::post('/api/solicitudes/verificar-disponibilidad', [SolicitudVisitaController::class, 'verificarDisponibilidad'])->name('solicitudes.verificar_disponibilidad');
});

// Rutas de Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/panel', [AdminController::class, 'panel'])->name('panel');
    Route::get('/ver-ci/{id}', [AdminController::class, 'verCI'])->name('ver_ci');
    Route::get('/vendedor/{id}/editar', [AdminController::class, 'editVendedor'])->name('editar_vendedor');        // ✅ corregido
    Route::put('/vendedor/{id}', [AdminController::class, 'updateVendedor'])->name('actualizar_vendedor');      // ✅ corregido
    Route::delete('/vendedor/{id}', [AdminController::class, 'deleteVendedor'])->name('eliminar_vendedor');     // ✅ corregido
    Route::get('/servir-ci/{id}', [AdminController::class, 'servirCI'])->name('servir_ci');
    Route::get('/documentos-propiedad/{id}', [DocumentoPropiedadController::class, 'verDocumento'])->name('documentos.ver');
    Route::post('/procesar-verificacion', [AdminController::class, 'procesarVerificacion'])->name('procesar_verificacion');
    Route::get('/historial', [AdminController::class, 'historial'])->name('historial');
    Route::post('/crear-vendedor', [AdminController::class, 'crearVendedor'])->name('crear_vendedor');
    Route::get('/minutas', [MinutaController::class, 'index'])->name('minutas.index');
    // Minutas
    Route::get('/minutas/create', [MinutaController::class, 'create'])->name('minutas.create');
    Route::post('/minutas', [MinutaController::class, 'store'])->name('minutas.store');

    // Moderación de anuncios
    Route::get('/moderacion', [AdminController::class, 'moderacionPanel'])->name('moderacion_panel');
    // Gestión de terrenos
    Route::get('/terrenos', [AdminController::class, 'terrenosPanel'])->name('terrenos_panel');
    Route::get('/terrenos/{id}', [AdminController::class, 'verTerreno'])->name('ver_terreno');
    Route::post('/procesar-terreno', [AdminController::class, 'procesarTerreno'])->name('procesar_terreno');

    // Control de lotes
    Route::get('/lotes', [AdminController::class, 'controlLotes'])->name('lotes');

    // Comprobantes IT
    Route::get('/comprobantes-it', [\App\Http\Controllers\Admin\ComprobanteItController::class, 'index'])->name('comprobantes_it.index');
    Route::post('/comprobantes-it/{id}/aprobar', [\App\Http\Controllers\Admin\ComprobanteItController::class, 'aprobar'])->name('comprobantes_it.aprobar');
    Route::post('/comprobantes-it/{id}/rechazar', [\App\Http\Controllers\Admin\ComprobanteItController::class, 'rechazar'])->name('comprobantes_it.rechazar');
    Route::get('/comprobantes-it/{id}/archivo', [\App\Http\Controllers\Admin\ComprobanteItController::class, 'verArchivo'])->name('comprobantes_it.archivo');
});
