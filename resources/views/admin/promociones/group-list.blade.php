@if($promos->isEmpty())
    <div class="card" style="background: rgba(0,0,0,0.1); border: 2px dashed rgba(255,255,255,0.1); box-shadow:none;">
        <div class="card-body" style="text-align:center; padding: 60px 20px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 48px; height: 48px; opacity: 0.3; margin-bottom: 15px; margin-left:auto; margin-right:auto; display:block; color:var(--text-muted);">
                <path d="M12 2L2 7l10 5 10-5-10-5z" />
                <path d="M2 17l10 5 10-5" />
                <path d="M2 12l10 5 10-5" />
            </svg>
            <h4 style="opacity:0.7; color: #fff;">No hay promociones pendientes</h4>
            <p style="opacity:0.5; font-size:0.9rem;">
                No se encontraron solicitudes de promoción pendientes para la categoría <strong>{{ $type }}</strong>.
            </p>
        </div>
    </div>
@else
    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap:24px;">
        @foreach($promos as $promo)
            @php
                $prop = $promo->promotable;
                $vendedor = $promo->promotable_type === 'App\Models\Terreno' ? $prop->vendedor : $prop->usuario;
            @endphp
            <div class="card" style="margin-bottom:0; display:flex; flex-direction:column; justify-content:space-between; border: 1px solid rgba(255,255,255,0.08); overflow:hidden;">
                
                <div>
                    <!-- Header with property type and date -->
                    <div style="padding: 16px 20px 0 20px; display:flex; justify-content:space-between; align-items:center; font-size:0.75rem;">
                        <span class="badge badge-secondary" style="text-transform:uppercase; letter-spacing:1px; padding: 3px 8px;">
                            {{ $promo->promotable_type === 'App\Models\Terreno' ? ($prop->tipo === 'lote' ? 'Lote' : 'Terreno') : 'Alquiler' }}
                        </span>
                        <span style="opacity:0.5;">
                            {{ $promo->created_at->format('d M, H:i') }}
                        </span>
                    </div>

                    <!-- Promo Info -->
                    <div style="padding: 12px 20px;">
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px; background: rgba(255,107,107,0.1); padding: 8px 12px; border-radius: 8px; border-left: 3px solid var(--accent);">
                            <span style="font-size: 1.6rem; font-weight: 900; color: var(--accent);">
                                -{{ number_format($promo->descuento_porcentaje, 0) }}%
                            </span>
                            <span style="font-size:0.8rem; font-weight:700; line-height:1.2; color:#fff;">
                                {{ $promo->titulo }}
                            </span>
                        </div>

                        <p style="font-size:0.85rem; opacity:0.85; line-height:1.4; margin:0 0 16px 0; background:rgba(0,0,0,0.15); padding:10px; border-radius:6px;">
                            "{{ $promo->descripcion }}"
                        </p>

                        <!-- Propiedad Vinculada Details -->
                        <div style="border-top: 1px solid rgba(255,255,255,0.05); padding-top:12px; margin-bottom:16px;">
                            <h4 style="font-size:0.95rem; font-weight:800; margin:0 0 4px 0; color:#fff;">
                                {{ $promo->promotable_type === 'App\Models\Terreno' ? $prop->nombre : $prop->titulo }}
                            </h4>
                            <div style="font-size:0.8rem; opacity:0.7; margin-bottom:4px;">
                                📍 {{ $prop->ubicacion }}
                            </div>
                            <div style="display:flex; justify-content:space-between; font-size:0.85rem; font-weight:600; color:var(--accent);">
                                <span>Precio Original:</span>
                                <span>
                                    @if($promo->promotable_type === 'App\Models\Terreno')
                                        ${{ number_format($prop->precio, 2) }}
                                    @else
                                        ${{ number_format($prop->precio_mensual, 2) }}/mes
                                    @endif
                                </span>
                            </div>
                            <div style="display:flex; justify-content:space-between; font-size:0.9rem; font-weight:700; color:var(--success); margin-top:2px;">
                                <span>Con Descuento:</span>
                                <span>
                                    @if($promo->promotable_type === 'App\Models\Terreno')
                                        ${{ number_format($prop->precio * (1 - $promo->descuento_porcentaje / 100), 2) }}
                                    @else
                                        ${{ number_format($prop->precio_mensual * (1 - $promo->descuento_porcentaje / 100), 2) }}/mes
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Vendedor Info -->
                        <div style="background: rgba(0,0,0,0.2); padding: 10px 12px; border-radius: 8px; font-size:0.8rem; border:1px solid rgba(255,255,255,0.03);">
                            <strong style="opacity:0.6; display:block; margin-bottom:2px;">Vendedor:</strong>
                            <div style="color:#fff; font-weight:600;">{{ $vendedor->nombre ?? 'Desconocido' }}</div>
                            <div style="opacity:0.7;">{{ $vendedor->correo ?? ($vendedor->email ?? '') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div style="padding: 16px 20px; background: rgba(0,0,0,0.15); border-top: 1px solid rgba(255,255,255,0.05);">
                    <div style="display:flex; gap:12px;">
                        <!-- Aprobar Form -->
                        <form action="{{ route('admin.moderacion.promocion.procesar', $promo->id) }}" method="POST" style="flex:1;">
                            @csrf
                            <input type="hidden" name="accion" value="aprobar">
                            <button type="submit" class="btn btn-success" style="width:100%; justify-content:center; padding:8px; font-size:0.9rem; border-radius:6px;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px; height:16px; margin-right:4px;">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                                Aprobar
                            </button>
                        </form>

                        <!-- Rechazar Toggle Button -->
                        <button type="button" class="btn btn-danger" onclick="toggleRejectionForm({{ $promo->id }})" style="flex:1; justify-content:center; padding:8px; font-size:0.9rem; border-radius:6px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px; height:16px; margin-right:4px;">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                            Rechazar
                        </button>
                    </div>

                    <!-- Rejection Form (Hidden by default) -->
                    <div id="rejection-form-{{ $promo->id }}" style="display:none; margin-top: 12px; padding-top: 12px; border-top: 1px dashed rgba(255,255,255,0.1);">
                        <form action="{{ route('admin.moderacion.promocion.procesar', $promo->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="accion" value="rechazar">
                            <div class="form-group" style="margin-bottom:8px;">
                                <label style="font-size:0.8rem; font-weight:600; color:#fff !important; display:block; margin-bottom:4px;">Motivo de Rechazo *</label>
                                <textarea name="motivo_rechazo" rows="2" class="form-control" placeholder="Escriba el motivo..." required style="width: 100%; font-size:0.8rem; padding: 6px 10px; border-radius: 6px; background: rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.15); color:#fff;"></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger btn-sm" style="width:100%; justify-content:center; font-size:0.8rem; padding:6px;">
                                Confirmar Rechazo
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        @endforeach
    </div>
@endif
