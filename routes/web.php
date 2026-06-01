<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VendedorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TerrenoController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\AlquilerController;
use App\Http\Controllers\DocumentoPropiedadController;
use App\Http\Controllers\SolicitudVisitaController;
use App\Http\Controllers\MinutaController;
use App\Http\Controllers\Admin\TramiteLegalController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\CategoriaController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas Públicas
Route::get('/terrenos', [TerrenoController::class, 'index'])->name('terrenos.index');
// NOTE: Lotes se exponen a través del catálogo (mismo layout que Terrenos)

// CATÁLOGO COMPRADOR (Marketplace) - Catálogo unificado
Route::get('/catalogo', [TerrenoController::class, 'catalogoUnificado'])->name('catalogo.unificado');

// CATÁLOGO COMPRADOR (Marketplace) - Terrenos independientes
Route::get('/catalogo/terrenos', [TerrenoController::class, 'catalogoTerrenos'])->name('catalogo.terrenos');

// Catálogo de Lotes — reutiliza TerrenoController::catalogoTerrenos con filtro 'tipo=lote'
Route::get('/catalogo/lotes', function(Request $request) {
    $request->merge(['tipo' => 'lote']);
    return app(\App\Http\Controllers\TerrenoController::class)->catalogoTerrenos($request);
})->name('catalogo.lotes');

// Detalle específico de lote (usa LoteController para asegurar tipo 'lote')
Route::get('/catalogo/lotes/{id}', [LoteController::class, 'detalle'])->name('catalogo.detalle.lote');

// Ruta genérica de detalle de catálogo (terrenos)
Route::get('/catalogo/{id}', [TerrenoController::class, 'detalle'])->name('catalogo.detalle');

// Rutas de Comprador (Requieren autenticación)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/catalogo/{id}/contactar', [\App\Http\Controllers\LeadController::class, 'store'])->name('catalogo.contactar');
    Route::post('/catalogo/alquiler/{id}/contactar', [\App\Http\Controllers\LeadController::class, 'storeAlquiler'])->name('catalogo.alquiler.contactar');
    Route::get('/mis-intereses', [\App\Http\Controllers\LeadController::class, 'indexComprador'])->name('comprador.leads');
    
    // Chat Unificado (Comprador/Vendedor)
    Route::get('/chat/{chat}', [\App\Http\Controllers\ChatController::class, 'show'])->name('chat.show');
    Route::get('/chat/{chat}/nuevos', [\App\Http\Controllers\ChatController::class, 'getNewMessages'])->name('chat.nuevos');
    Route::post('/chat/{chat}/mensaje', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/archivo/{mensaje}', [\App\Http\Controllers\ChatController::class, 'downloadArchivo'])->name('chat.archivo');
    
    // ── Favoritos (IN-C05) ──
    Route::get('/mis-favoritos', [\App\Http\Controllers\FavoritoController::class, 'index'])->name('favoritos.index');
    Route::post('/favoritos/toggle', [\App\Http\Controllers\FavoritoController::class, 'toggle'])->name('favoritos.toggle');
});

// CATÁLOGO COMPRADOR (Marketplace) - Alquileres (público)
Route::get('/alquileres', [AlquilerController::class, 'catalogo'])->name('catalogo.alquileres');
Route::get('/alquileres/{id}', [AlquilerController::class, 'detalle'])->name('catalogo.detalle.alquiler');

// Autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/registro', [App\Http\Controllers\RegistroController::class, 'showRegister'])->name('registro');
Route::post('/registro', [App\Http\Controllers\RegistroController::class, 'register'])->name('registro.post');

// ── Verificación de correo electrónico (Laravel Email Verification) ────────
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('login')->with('success', '¡Correo verificado exitosamente! Ya puedes iniciar sesión.');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'El enlace de verificación ha sido reenviado a tu correo.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Rutas de Vendedor
Route::middleware(['auth', 'verified', 'role:vendedor'])->prefix('vendedor')->name('vendedor.')->group(function () {
    Route::get('/dashboard', [VendedorController::class, 'dashboard'])->name('dashboard');
    Route::get('/propiedades', [VendedorController::class, 'propiedadesPanel'])->name('propiedades_panel');
    Route::post('/subir-ci', [VendedorController::class, 'subirCI'])->name('subir_ci');
    Route::delete('/eliminar-ci', [VendedorController::class, 'eliminarCI'])->name('eliminar_ci');
    Route::get('/mi-ci', [VendedorController::class, 'servirMiCI'])->name('mi_ci');
    Route::get('/historial', [VendedorController::class, 'historialPropio'])->name('historial');
    Route::post('/terrenos/{id}/toggle-estado', [TerrenoController::class, 'toggleEstado'])->name('terrenos.toggle_estado');

    // Ruta unificada para publicar cualquier tipo de propiedad
    Route::get('/publicar-propiedad', [TerrenoController::class, 'create'])->name('publicar_propiedad');

    // Terrenos
    Route::delete('/terreno-imagen/{id}', [TerrenoController::class, 'eliminarImagen'])->name('terrenos.imagen.destroy');
    Route::get('/terrenos/crear', [TerrenoController::class, 'create'])->name('terrenos.create');
    Route::post('/terrenos', [TerrenoController::class, 'store'])->name('terrenos.store');
    Route::get('/mis-terrenos', function() { return redirect()->route('vendedor.propiedades_panel', ['tipo' => 'terreno']); })->name('terrenos.mis');
    Route::get('/terrenos/editar/{id}', [TerrenoController::class, 'edit'])->name('terrenos.edit');
    Route::put('/terrenos/{id}', [TerrenoController::class, 'update'])->name('terrenos.update');

    // Alquileres (gestión del vendedor)
    Route::get('/alquileres', [AlquilerController::class, 'index'])->name('alquileres.index');
    Route::get('/alquileres/crear', [AlquilerController::class, 'create'])->name('alquileres.create');
    Route::post('/alquileres', [AlquilerController::class, 'store'])->name('alquileres.store');
    Route::get('/mis-alquileres', function() { return redirect()->route('vendedor.propiedades_panel', ['tipo' => 'alquiler']); })->name('alquileres.mis');
    Route::get('/alquileres/editar/{id}', [AlquilerController::class, 'edit'])->name('alquileres.edit');
    Route::put('/alquileres/{id}', [AlquilerController::class, 'update'])->name('alquileres.update');
    Route::delete('/alquileres/{id}', [AlquilerController::class, 'destroy'])->name('alquileres.destroy');
    Route::post('/alquileres/toggle-estado/{id}', [AlquilerController::class, 'toggleEstado'])->name('alquileres.toggle_estado');
    Route::delete('/alquiler-imagen/{id}', [AlquilerController::class, 'eliminarImagen'])->name('alquileres.imagen.destroy');

    // Documentos propiedad
    Route::get('/terrenos/{id}/documentos', [DocumentoPropiedadController::class, 'mostrarFormularioSubida'])->name('documentos.subir');
    Route::post('/terrenos/{id}/documentos', [DocumentoPropiedadController::class, 'subirDocumento'])->name('documentos.store');
    Route::delete('/terrenos/{id}/documentos', [DocumentoPropiedadController::class, 'eliminarDocumento'])->name('documentos.destroy');
    Route::get('/documentos-propiedad/{id}', [DocumentoPropiedadController::class, 'verDocumento'])->name('documentos.ver');

    // Control de lotes
    Route::get('/lotes', function() { return redirect()->route('vendedor.propiedades_panel', ['tipo' => 'lote']); })->name('lotes');
    Route::post('/lotes/{id}/estado', [VendedorController::class, 'updateLoteEstado'])->name('lotes.estado');

    // Comprobante IT
    Route::get('/comprobante-it', [\App\Http\Controllers\Vendedor\ComprobanteItController::class, 'index'])->name('comprobante_it');
    Route::post('/comprobante-it', [\App\Http\Controllers\Vendedor\ComprobanteItController::class, 'store'])->name('comprobante_it.store');

    // Proceso Legal de Venta
    Route::get('/proceso-legal', [MinutaController::class, 'tramiteLegal'])->name('proceso_legal');
    Route::get('/historial-legal', [MinutaController::class, 'historialLegal'])->name('historial_legal');
    Route::get('/minuta/{id}/archivo', [MinutaController::class, 'verArchivo'])->name('minuta.archivo');
    Route::get('/comprobante-it/{id}/archivo', [\App\Http\Controllers\Vendedor\ComprobanteItController::class, 'verArchivo'])->name('comprobante_it.archivo');
    Route::post('/proceso-legal/minuta', [MinutaController::class, 'storeVendedor'])->name('proceso_legal.minuta.store');
    
    // Protocolización
    Route::post('/proceso-legal/protocolizacion', [\App\Http\Controllers\Vendedor\ProtocolizacionController::class, 'store'])->name('proceso_legal.protocolizacion.store');
    Route::get('/protocolizacion/{id}/archivo', [\App\Http\Controllers\Vendedor\ProtocolizacionController::class, 'verArchivo'])->name('protocolizacion.archivo');

    // Leads
    Route::get('/leads', [\App\Http\Controllers\LeadController::class, 'indexVendedor'])->name('leads.index');
    Route::post('/leads/{lead}/estado', [\App\Http\Controllers\LeadController::class, 'updateEstado'])->name('leads.estado');

    // Promociones (OBS-A02)
    Route::get('/promociones', [\App\Http\Controllers\Vendedor\PromocionController::class, 'index'])->name('promociones.index');
    Route::get('/promociones/crear', [\App\Http\Controllers\Vendedor\PromocionController::class, 'create'])->name('promociones.create');
    Route::post('/promociones', [\App\Http\Controllers\Vendedor\PromocionController::class, 'store'])->name('promociones.store');
});

// Rutas compartidas (vendedor y admin) para solicitudes de visita
Route::middleware(['auth', 'verified', 'role:vendedor,admin'])->prefix('vendedor')->name('vendedor.')->group(function () {
    Route::get('/solicitudes', [SolicitudVisitaController::class, 'index'])->name('solicitudes.index');
    Route::get('/solicitudes/calendario', [SolicitudVisitaController::class, 'calendario'])->name('solicitudes.calendario');
    Route::get('/solicitudes/crear', [SolicitudVisitaController::class, 'create'])->name('solicitudes.create');
    Route::post('/solicitudes', [SolicitudVisitaController::class, 'store'])->name('solicitudes.store');
    Route::get('/solicitudes/{id}', [SolicitudVisitaController::class, 'show'])->name('solicitudes.show');
    Route::post('/solicitudes/{id}/aprobar', [SolicitudVisitaController::class, 'aprobar'])->name('solicitudes.aprobar');
    Route::post('/solicitudes/{id}/rechazar', [SolicitudVisitaController::class, 'rechazar'])->name('solicitudes.rechazar');
    Route::post('/solicitudes/{id}/cancelar', [SolicitudVisitaController::class, 'cancelar'])->name('solicitudes.cancelar');
    Route::get('/propiedad/{tipo}/{id}/solicitudes', [SolicitudVisitaController::class, 'solicitudesPorPropiedad'])->name('propiedades.solicitudes');
    Route::post('/solicitudes/{id}/reprogramar', [SolicitudVisitaController::class, 'reprogramar'])->name('solicitudes.reprogramar');
    Route::get('/api/solicitudes/eventos', [SolicitudVisitaController::class, 'eventos'])->name('solicitudes.eventos');
    Route::post('/api/solicitudes/verificar-disponibilidad', [SolicitudVisitaController::class, 'verificarDisponibilidad'])->name('solicitudes.verificar_disponibilidad');
});

// Rutas de Admin
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/panel', [AdminController::class, 'panel'])->name('panel');
    Route::get('/ver-ci/{id}', [AdminController::class, 'verCI'])->name('ver_ci');
    Route::get('/vendedor/{id}/editar', [AdminController::class, 'editVendedor'])->name('editar_vendedor');
    Route::put('/vendedor/{id}', [AdminController::class, 'updateVendedor'])->name('actualizar_vendedor');
    Route::delete('/vendedor/{id}', [AdminController::class, 'deleteVendedor'])->name('eliminar_vendedor');
    Route::get('/servir-ci/{id}', [AdminController::class, 'servirCI'])->name('servir_ci');
    Route::get('/documentos-propiedad/{id}', [DocumentoPropiedadController::class, 'verDocumento'])->name('documentos.ver');
    Route::post('/procesar-verificacion', [AdminController::class, 'procesarVerificacion'])->name('procesar_verificacion');
    Route::get('/historial', [AdminController::class, 'historial'])->name('historial');
    Route::post('/crear-vendedor', [AdminController::class, 'crearVendedor'])->name('crear_vendedor');
    Route::get('/minutas', [MinutaController::class, 'index'])->name('minutas.index');

    // Gestión de folios
    Route::get('/folios', [AdminController::class, 'foliosPanel'])->name('folios_panel');
    Route::post('/folios/verificar', [AdminController::class, 'verificarFolio'])->name('folio.verificar');

    // Inscripciones Derechos Reales (admin)
    Route::get('/inscripciones', [InscripcionController::class, 'adminIndex'])->name('inscripciones');
    Route::post('/inscripciones/procesar', [InscripcionController::class, 'adminProcesar'])->name('inscripcion.procesar');
    Route::get('/inscripciones/{id}/archivo', [InscripcionController::class, 'verArchivo'])->name('inscripcion.archivo');

    // Minutas
    Route::get('/minutas/create', [MinutaController::class, 'create'])->name('minutas.create');
    Route::post('/minutas', [MinutaController::class, 'store'])->name('minutas.store');

    // Moderación de anuncios
    Route::get('/moderacion', [AdminController::class, 'moderacionPanel'])->name('moderacion_panel');
    Route::post('/moderacion/promocion/{id}', [AdminController::class, 'procesarPromocion'])->name('moderacion.promocion.procesar');

    // Gestor de Propiedades unificado (OBS-A01)
    Route::get('/propiedades', [AdminController::class, 'propiedadesPanel'])->name('propiedades_panel');
    Route::get('/terrenos', function() { return redirect()->route('admin.propiedades_panel'); })->name('terrenos_panel');
    Route::get('/alquileres', function() { return redirect()->route('admin.propiedades_panel'); })->name('alquileres_panel');
    Route::get('/lotes', function() { return redirect()->route('admin.propiedades_panel'); })->name('lotes');

    // Rutas de revisión y acciones sobre terrenos/lotes/alquileres
    Route::get('/terrenos/{id}', [AdminController::class, 'verTerreno'])->name('ver_terreno');
    Route::put('/terrenos/{id}/coordenadas', [AdminController::class, 'actualizarCoordenadas'])->name('terreno.actualizar_coordenadas');
    Route::post('/procesar-terreno', [AdminController::class, 'procesarTerreno'])->name('procesar_terreno');
    Route::get('/alquileres/{id}', [AdminController::class, 'verAlquiler'])->name('ver_alquiler');
    Route::post('/procesar-alquiler', [AdminController::class, 'procesarAlquiler'])->name('procesar_alquiler');

    // Endpoint para el reporte de inventario
    Route::get('/api/inventario-stats', [AdminController::class, 'getInventarioStats'])->name('api.inventario_stats');

    // Auditoría
    Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria');
    Route::get('/auditoria/exportar', [AuditoriaController::class, 'exportarCsv'])->name('auditoria.exportar');

    // Comprobantes IT
    Route::get('/comprobantes-it', [\App\Http\Controllers\Admin\ComprobanteItController::class, 'index'])->name('comprobantes_it.index');
    Route::post('/comprobantes-it/{id}/aprobar', [\App\Http\Controllers\Admin\ComprobanteItController::class, 'aprobar'])->name('comprobantes_it.aprobar');
    Route::post('/comprobantes-it/{id}/rechazar', [\App\Http\Controllers\Admin\ComprobanteItController::class, 'rechazar'])->name('comprobantes_it.rechazar');
    Route::get('/comprobantes-it/{id}/archivo', [\App\Http\Controllers\Admin\ComprobanteItController::class, 'verArchivo'])->name('comprobantes_it.archivo');

    // Gestión Legal unificada (Admin)
    Route::get('/tramites-legales', [TramiteLegalController::class, 'index'])->name('tramites_legales.index');
    Route::post('/tramites-legales/{id}/aprobar-minuta', [TramiteLegalController::class, 'aprobarMinuta'])->name('tramites_legales.aprobar_minuta');
    Route::post('/tramites-legales/{id}/rechazar-minuta', [TramiteLegalController::class, 'rechazarMinuta'])->name('tramites_legales.rechazar_minuta');
    Route::post('/tramites-legales/{id}/aprobar-it', [TramiteLegalController::class, 'aprobarIT'])->name('tramites_legales.aprobar_it');
    Route::post('/tramites-legales/{id}/rechazar-it', [TramiteLegalController::class, 'rechazarIT'])->name('tramites_legales.rechazar_it');
    Route::post('/tramites-legales/{id}/finalizar', [TramiteLegalController::class, 'finalizarTramite'])->name('tramites_legales.finalizar');
    Route::get('/tramites-legales/{id}/minuta-archivo', [TramiteLegalController::class, 'verMinuta'])->name('tramites_legales.ver_minuta');
    
    Route::post('/tramites-legales/{id}/aprobar-protocolizacion', [TramiteLegalController::class, 'aprobarProtocolizacion'])->name('tramites_legales.aprobar_protocolizacion');
    Route::post('/tramites-legales/{id}/rechazar-protocolizacion', [TramiteLegalController::class, 'rechazarProtocolizacion'])->name('tramites_legales.rechazar_protocolizacion');
    Route::get('/tramites-legales/{id}/testimonio', [TramiteLegalController::class, 'verTestimonio'])->name('tramites_legales.ver_testimonio');

    // Gestión de Categorías (IN-A02)
    Route::resource('categorias', CategoriaController::class)
         ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
         ->names('categorias');

    // Supervisión de Leads y Chats
    Route::get('/supervision-leads', [\App\Http\Controllers\Admin\SupervisionController::class, 'indexLeads'])->name('supervision.leads');
    Route::get('/supervision-chat/{chat}', [\App\Http\Controllers\Admin\SupervisionController::class, 'showChat'])->name('supervision.chat');

    // Gestión de Administradores Adicionales (OBS-A05)
    Route::get('/administradores', [AdminController::class, 'administradoresIndex'])->name('administradores.index');
    Route::post('/administradores', [AdminController::class, 'crearAdministrador'])->name('administradores.store');
});

Route::get('/mapa', [App\Http\Controllers\MapaController::class, 'index'])->name('mapa.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/consulta-folio', [App\Http\Controllers\FolioConsultaController::class, 'form'])->name('folio.consultar.form');
    Route::post('/consulta-folio', [App\Http\Controllers\FolioConsultaController::class, 'consultar'])->name('folio.consultar.post');
    Route::get('/folio/{id}/completo', [App\Http\Controllers\FolioConsultaController::class, 'completo'])->name('folio.completo');
});

// Rutas de folio para vendedor
Route::middleware(['auth', 'verified', 'role:vendedor'])->prefix('vendedor')->name('vendedor.')->group(function () {
    Route::get('/terrenos/{id}/folio/crear', [App\Http\Controllers\FolioVendedorController::class, 'create'])->name('folio.create');
    Route::post('/terrenos/{id}/folio', [App\Http\Controllers\FolioVendedorController::class, 'store'])->name('folio.store');
    Route::get('/terrenos/{id}/folio/editar', [App\Http\Controllers\FolioVendedorController::class, 'edit'])->name('folio.edit');
    Route::put('/terrenos/{id}/folio', [App\Http\Controllers\FolioVendedorController::class, 'update'])->name('folio.update');
    // Inscripción Derechos Reales (vendedor)
    Route::get('/folio/{folioId}/inscripcion', [InscripcionController::class, 'create'])->name('inscripcion.create');
    Route::post('/folio/{folioId}/inscripcion', [InscripcionController::class, 'store'])->name('inscripcion.store');
    Route::get('/inscripcion/{id}/archivo', [InscripcionController::class, 'verArchivo'])->name('inscripcion.archivo');

    // Expediente Legal unificado (OBS-V09)
    Route::get('/terrenos/{id}/expediente', [\App\Http\Controllers\VendedorExpedienteController::class, 'show'])->name('expediente');
});
