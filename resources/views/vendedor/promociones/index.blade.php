@extends('layouts.app')

@section('title', 'Mis Promociones y Descuentos')

@section('content')
<div style="display:flex; flex-direction:column; gap:24px;">

    <!-- CABECERA -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
        <div>
            <p style="margin: 0; opacity: 0.8; font-size: 0.95rem;">
                Administre las ofertas y descuentos de sus terrenos, lotes o alquileres aprobados.
            </p>
        </div>
        <a href="{{ route('vendedor.promociones.create') }}" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:8px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Postular Promoción
        </a>
    </div>

    <!-- LISTADO -->
    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap:20px;">
        @forelse($promociones as $promo)
            <div class="card" style="margin-bottom:0; border-top: 4px solid {{ $promo->estado === 'aprobado' ? 'var(--success)' : ($promo->estado === 'rechazado' ? 'var(--danger)' : 'var(--warning)') }};">
                <div class="card-body" style="display:flex; flex-direction:column; height:100%; justify-content:space-between;">
                    
                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                            <div>
                                <span style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1px; opacity:0.6;">
                                    {{ $promo->promotable_type === 'App\Models\Terreno' ? ($promo->promotable->tipo === 'lote' ? 'Lote' : 'Terreno') : 'Alquiler' }}
                                </span>
                                <h3 style="font-size: 1.15rem; font-weight: 800; margin: 4px 0 0 0; color: #fff;">
                                    {{ $promo->titulo }}
                                </h3>
                            </div>
                            
                            @if($promo->estado === 'pendiente')
                                <span class="badge badge-warning" style="padding: 4px 8px; font-size: 0.75rem;">En revisión</span>
                            @elseif($promo->estado === 'aprobado')
                                <span class="badge badge-success" style="padding: 4px 8px; font-size: 0.75rem;">Aprobado</span>
                            @else
                                <span class="badge badge-danger" style="padding: 4px 8px; font-size: 0.75rem;">Rechazado</span>
                            @endif
                        </div>

                        <!-- Info de descuento -->
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px; background: rgba(255, 255, 255, 0.05); padding: 8px 12px; border-radius: 6px;">
                            <span style="font-size: 1.5rem; font-weight: 900; color: var(--accent);">
                                {{ number_format($promo->descuento_porcentaje, 0) }}%
                            </span>
                            <span style="font-size:0.8rem; opacity:0.8; line-height:1.2;">
                                de Descuento aplicado
                            </span>
                        </div>

                        <!-- Propiedad vinculada -->
                        <div style="margin-bottom:12px; font-size:0.9rem;">
                            <strong style="opacity:0.7;">Propiedad:</strong>
                            <div style="font-weight:600; color: #fff; margin-top:2px;">
                                @if($promo->promotable_type === 'App\Models\Terreno')
                                    {{ $promo->promotable->nombre }} (Cod: {{ $promo->promotable->codigo }})
                                @else
                                    {{ $promo->promotable->titulo }}
                                @endif
                            </div>
                            <div style="font-size: 0.8rem; opacity:0.6; margin-top:2px;">
                                Ubicación: {{ $promo->promotable->ubicacion }}
                            </div>
                        </div>

                        <p style="font-size:0.85rem; opacity:0.8; line-height:1.4; margin:0 0 16px 0;">
                            {{ $promo->descripcion }}
                        </p>
                    </div>

                    <div>
                        @if($promo->estado === 'rechazado' && $promo->motivo_rechazo)
                            <div class="alert alert-error" style="margin-bottom: 12px; border-radius:6px; padding:10px 12px; font-size:0.8rem; border-left:3px solid var(--danger);">
                                <strong style="display:block; margin-bottom:2px;">Motivo del Rechazo:</strong>
                                {{ $promo->motivo_rechazo }}
                            </div>
                        @endif

                        <div style="font-size: 0.75rem; opacity: 0.5; text-align:right; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 8px;">
                            Postulado: {{ $promo->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>

                </div>
            </div>
        @empty
            <div class="card" style="grid-column: 1 / -1; background: rgba(0,0,0,0.1); border: 2px dashed rgba(255,255,255,0.1); box-shadow:none;">
                <div class="card-body" style="text-align:center; padding: 60px 20px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 48px; height: 48px; opacity: 0.3; margin-bottom: 15px; margin-left:auto; margin-right:auto; display:block;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <h4 style="opacity:0.7; color: #fff;">No hay promociones postuladas</h4>
                    <p style="opacity:0.5; font-size:0.9rem; margin-bottom: 20px;">
                        ¿Desea impulsar sus ventas ofreciendo un descuento en sus terrenos o alquileres?
                    </p>
                    <a href="{{ route('vendedor.promociones.create') }}" class="btn btn-primary btn-sm">
                        Crear mi Primera Promoción
                    </a>
                </div>
            </div>
        @endforelse
    </div>

</div>
@endsection
