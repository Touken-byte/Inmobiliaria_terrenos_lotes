@extends('layouts.app')

@section('content')
<div style="background:#0b1220; color:#e5e7eb; padding:1.5rem; border-radius:8px;">
    <h2 style="margin:0 0 1rem 0;">Expediente Legal — {{ $terreno->nombre ?? 'Propiedad' }}</h2>

    <div style="margin-bottom: 24px;">
        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
            <span style="color:#a0a0b0; font-size:0.85rem;">Progreso del expediente</span>
            <span style="color:#eab308; font-weight:600;">{{ $completadas }}/7 etapas</span>
        </div>
        <div style="background:rgba(255,255,255,0.08); border-radius:999px; height:8px;">
            <div style="background:#eab308; border-radius:999px; height:8px; width:{{ round(($completadas/7)*100) }}%; transition: width 0.3s;"></div>
        </div>
    </div>

    <div style="display:flex; gap:1rem;">
        {{-- Sidebar tabs --}}
        <div style="width:260px;">
            @php
                $tabs = [
                    'minuta' => 'Minuta de Compraventa',
                    'it' => 'Impuesto de Transferencia (IT)',
                    'plazo' => 'Validación Plazo IT',
                    'protocolizacion' => 'Protocolización Notarial',
                    'inscripcion' => 'Inscripción Derechos Reales',
                    'folio' => 'Folio Real',
                    'alertas' => 'Alertas Legales',
                ];
            @endphp
            <ul style="list-style:none; padding:0; margin:0;">
                @foreach($tabs as $k => $label)
                    <li style="margin-bottom:8px;">
                        <a href="#{{ $k }}" style="display:block; padding:10px; border-radius:8px; background:#071029; color:#cbd5e1; text-decoration:none; border:1px solid rgba(255,255,255,0.02);">{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Content panes --}}
        <div style="flex:1; background:#071023; padding:1rem; border-radius:8px; border:1px solid rgba(255,255,255,0.03);">
            {{-- SECCIÓN 1: Minuta --}}
            <section id="minuta" style="margin-bottom:1rem;">
                <h3 style="margin:0 0 0.5rem 0;">1. Minuta de Compraventa</h3>
                @if($minuta)
                    <div style="display:flex; gap:1rem; align-items:center;">
                        <div>
                            <div><strong>Fecha:</strong> {{ $minuta->fecha?->format('Y-m-d') }}</div>
                            <div><strong>Monto:</strong> {{ $minuta->monto }}</div>
                            <div><strong>Estado:</strong> <span style="color:{{ $minuta->estado === 'aprobada' ? '#10b981' : ($minuta->estado === 'rechazada' ? '#ef4444' : '#f59e0b') }}">{{ ucfirst($minuta->estado) }}</span></div>
                        </div>
                        @if($minuta->archivo)
                            <a href="{{ route('minuta.archivo', $minuta->id) }}" class="btn btn-sm" style="margin-left:auto; background:#111827; color:#eab308; padding:6px 10px; border-radius:6px;">Ver PDF</a>
                        @endif
                    </div>
                @else
                    <div style="display:flex; align-items:center; gap:1rem;">
                        <div>No se ha registrado una minuta para esta propiedad.</div>
                        <a href="{{ route('vendedor.proceso_legal') }}" class="btn btn-sm" style="margin-left:auto; background:#0ea5e9; color:#041025; padding:6px 10px; border-radius:6px;">Registrar Minuta</a>
                    </div>
                @endif
            </section>

            {{-- SECCIÓN 2: Comprobante IT --}}
            <section id="it" style="margin-bottom:1rem;">
                <h3 style="margin:0 0 0.5rem 0;">2. Impuesto de Transferencia (IT)</h3>
                @if($minuta && $minuta->comprobante)
                    <div>
                        <div><strong>Fecha pago:</strong> {{ $minuta->comprobante->fecha_pago?->format('Y-m-d') }}</div>
                        <div><strong>Monto:</strong> {{ $minuta->comprobante->monto }}</div>
                        <div><strong>Nº recibo:</strong> {{ $minuta->comprobante->numero_recibo }}</div>
                        <div style="margin-top:6px;">
                            <a href="{{ route('comprobante_it.archivo', $minuta->comprobante->id) }}" class="btn btn-sm" style="background:#111827; color:#eab308; padding:6px 10px; border-radius:6px;">Ver PDF</a>
                            @if($minuta->comprobante->alerta_multa)
                                <span style="margin-left:8px; background:#7f1d1d; color:#fee2e2; padding:4px 8px; border-radius:6px;">Alerta multa</span>
                            @endif
                        </div>
                    </div>
                @else
                    <div style="display:flex; align-items:center; gap:1rem;">
                        <div>No hay comprobante IT registrado.</div>
                        <a href="{{ route('vendedor.proceso_legal') }}" class="btn btn-sm" style="margin-left:auto; background:#06b6d4; color:#041025; padding:6px 10px; border-radius:6px;">Subir Comprobante IT</a>
                    </div>
                @endif
            </section>

            {{-- SECCIÓN 3: Validación Plazo IT --}}
            <section id="plazo" style="margin-bottom:1rem;">
                <h3 style="margin:0 0 0.5rem 0;">3. Validación Plazo IT</h3>
                @php
                    $plazoStatus = 'sin-datos';
                    $dias = null;
                    if($minuta && $minuta->comprobante && $minuta->fecha && $minuta->comprobante->fecha_pago) {
                        $inicio = \Carbon\Carbon::parse($minuta->fecha);
                        $fin = \Carbon\Carbon::parse($minuta->comprobante->fecha_pago);
                        // cálculo simple de días corridos (no hábiles) por simplicidad
                        $dias = $fin->diffInDaysFiltered(function($date){ return $date->isWeekday(); }, $inicio, $fin);
                        $plazoStatus = $dias <= 10 ? 'ok' : 'late';
                    } elseif($minuta && !$minuta->comprobante) {
                        $plazoStatus = 'missing';
                    }
                @endphp
                @if($plazoStatus === 'ok')
                    <div style="color:#10b981;">OK — {{ $dias }} días hábiles (≤ 10)</div>
                @elseif($plazoStatus === 'late')
                    <div style="color:#ef4444;">Fuera de plazo — {{ $dias }} días hábiles (> 10)</div>
                @elseif($plazoStatus === 'missing')
                    <div style="color:#f59e0b;">Pendiente: aún no se registró comprobante IT</div>
                @else
                    <div style="color:#94a3b8;">Sin información suficiente</div>
                @endif
            </section>

            {{-- SECCIÓN 4: Protocolización --}}
            <section id="protocolizacion" style="margin-bottom:1rem;">
                <h3 style="margin:0 0 0.5rem 0;">4. Protocolización Notarial</h3>
                @if($minuta && $minuta->protocolizacion)
                    <div>
                        <div><strong>Nº Protocolo:</strong> {{ $minuta->protocolizacion->numero_protocolo }}</div>
                        <div><strong>Fecha:</strong> {{ $minuta->protocolizacion->fecha_protocolizacion?->format('Y-m-d') }}</div>
                        <div><strong>Estado:</strong> {{ ucfirst($minuta->protocolizacion->estado) }}</div>
                        @if($minuta->protocolizacion->archivo_testimonio)
                            <a href="{{ route('protocolizacion.archivo', $minuta->protocolizacion->id) }}" class="btn btn-sm" style="margin-top:6px; background:#111827; color:#eab308; padding:6px 10px; border-radius:6px;">Ver Testimonio</a>
                        @endif
                    </div>
                @else
                    <div style="display:flex; align-items:center; gap:1rem;">
                        <div>No hay protocolización registrada.</div>
                        <a href="{{ route('vendedor.proceso_legal') }}" class="btn btn-sm" style="margin-left:auto; background:#7c3aed; color:#fff; padding:6px 10px; border-radius:6px;">Registrar Protocolización</a>
                    </div>
                @endif
            </section>

            {{-- SECCIÓN 5: Inscripción Derechos Reales --}}
            <section id="inscripcion" style="margin-bottom:1rem;">
                <h3 style="margin:0 0 0.5rem 0;">5. Inscripción Derechos Reales</h3>
                @if($terreno->folio && $terreno->folio->inscripcionDerechosReales)
                    @php $ins = $terreno->folio->inscripcionDerechosReales; @endphp
                    <div>
                        <div><strong>Nº Matrícula:</strong> {{ $ins->numero_matricula }}</div>
                        <div><strong>Fecha entrada:</strong> {{ $ins->fecha_entrada?->format('Y-m-d') }}</div>
                        <div><strong>Fecha salida:</strong> {{ $ins->fecha_salida?->format('Y-m-d') }}</div>
                        <div><strong>Tasa pagada:</strong> {{ $ins->tasa_pagada }}</div>
                        <div><strong>Estado:</strong> {{ ucfirst($ins->estado) }}</div>
                    </div>
                @else
                    <div style="display:flex; align-items:center; gap:1rem;">
                        <div>No existe inscripción registrada.</div>
                        @if($terreno->folio)
                            <a href="{{ route('inscripcion.create', $terreno->folio->id) }}" class="btn btn-sm" style="margin-left:auto; background:#0ea5e9; color:#041025; padding:6px 10px; border-radius:6px;">Iniciar Inscripción</a>
                        @else
                            <div style="color:#94a3b8; margin-left:auto;">Agrega un folio para iniciar la inscripción.</div>
                        @endif
                    </div>
                @endif
            </section>

            {{-- SECCIÓN 6: Folio Real --}}
            <section id="folio" style="margin-bottom:1rem;">
                <h3 style="margin:0 0 0.5rem 0;">6. Folio Real</h3>
                @if($terreno->folio)
                    <div>
                        <div><strong>Nº Folio:</strong> {{ $terreno->folio->numero_folio }}</div>
                        <div><strong>Superficie:</strong> {{ $terreno->folio->superficie }}</div>
                        <div><strong>Estado:</strong> <span style="color:{{ $terreno->folio->estado === 'verificado' ? '#10b981' : '#f59e0b' }}">{{ $terreno->folio->estado }}</span></div>
                        <div><strong>Colindancias:</strong> {{ $terreno->folio->colindancias }}</div>
                        <div style="margin-top:6px;">
                            <a href="{{ route('vendedor.folio.edit', $terreno->id) }}" class="btn btn-sm" style="background:#111827; color:#eab308; padding:6px 10px; border-radius:6px;">Editar Folio</a>
                        </div>
                    </div>
                @else
                    <div style="display:flex; align-items:center; gap:1rem;">
                        <div>No hay folio registrado para esta propiedad.</div>
                        <a href="{{ route('vendedor.folio.create', $terreno->id) }}" class="btn btn-sm" style="margin-left:auto; background:#f59e0b; color:#041025; padding:6px 10px; border-radius:6px;">Agregar Folio</a>
                    </div>
                @endif
            </section>

            {{-- SECCIÓN 7: Alertas Legales --}}
            <section id="alertas" style="margin-bottom:1rem;">
                <h3 style="margin:0 0 0.5rem 0;">7. Alertas Legales</h3>
                @php
                    $alerts = collect();
                    if($terreno->folio) {
                        $alerts = $alerts->merge(App\Models\AlertaLegal::where('alertable_type', App\Models\Folio::class)->where('alertable_id', $terreno->folio->id)->where('estado','pendiente')->get());
                    }
                    $alerts = $alerts->merge($alertasTerreno->where('estado','pendiente'));
                @endphp

                @if($alerts->count() > 0)
                    <div style="background:#331b1b; color:#fee2e2; padding:8px; border-radius:6px;">Hay <strong>{{ $alerts->count() }}</strong> alertas pendientes</div>
                    <ul style="margin-top:8px;">
                        @foreach($alerts as $a)
                            <li style="margin-bottom:6px;">• <strong>{{ $a->tipo }}:</strong> {{ $a->mensaje }}</li>
                        @endforeach
                    </ul>
                @else
                    <div style="color:#10b981;">Sin inconsistencias detectadas</div>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection
