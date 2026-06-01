@extends('layouts.app')

@section('title', 'Moderación de Anuncios y Promociones')

@section('content')
<!-- Stats Cards -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 24px;">
    <div class="stat-card" style="border-left: 4px solid var(--warning);">
        <div class="stat-icon" style="color: var(--warning);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px; height:24px;">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12,6 12,12 16,14" />
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-number" style="font-size: 1.8rem; font-weight: 800; color: #fff;">{{ $stats->pendientes }}</span>
            <span class="stat-label" style="font-size: 0.85rem; opacity: 0.7;">Promociones Pendientes</span>
        </div>
    </div>
    
    <div class="stat-card" style="border-left: 4px solid var(--success);">
        <div class="stat-icon" style="color: var(--success);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px; height:24px;">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
                <polyline points="22,4 12,14.01 9,11.01" />
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-number" style="font-size: 1.8rem; font-weight: 800; color: #fff;">{{ $stats->aprobados }}</span>
            <span class="stat-label" style="font-size: 0.85rem; opacity: 0.7;">Promociones Aprobadas</span>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid var(--danger);">
        <div class="stat-icon" style="color: var(--danger);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px; height:24px;">
                <circle cx="12" cy="12" r="10" />
                <line x1="15" y1="9" x2="9" y2="15" />
                <line x1="9" y1="9" x2="15" y2="15" />
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-number" style="font-size: 1.8rem; font-weight: 800; color: #fff;">{{ $stats->rechazados }}</span>
            <span class="stat-label" style="font-size: 0.85rem; opacity: 0.7;">Promociones Rechazadas</span>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="tabs-container" style="margin-bottom: 24px; display: flex; gap: 8px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px;">
    <button class="tab-btn active" data-tab="terrenos" onclick="switchTab('terrenos')">
        Terrenos ({{ count($terrenos) }})
    </button>
    <button class="tab-btn" data-tab="lotes" onclick="switchTab('lotes')">
        Lotes ({{ count($lotes) }})
    </button>
    <button class="tab-btn" data-tab="alquileres" onclick="switchTab('alquileres')">
        Alquileres ({{ count($alquileres) }})
    </button>
</div>

<!-- Grid containers for each group -->
<div id="tab-content-terrenos" class="tab-content-pane active">
    @include('admin.promociones.group-list', ['promos' => $terrenos, 'type' => 'Terrenos'])
</div>

<div id="tab-content-lotes" class="tab-content-pane" style="display:none;">
    @include('admin.promociones.group-list', ['promos' => $lotes, 'type' => 'Lotes'])
</div>

<div id="tab-content-alquileres" class="tab-content-pane" style="display:none;">
    @include('admin.promociones.group-list', ['promos' => $alquileres, 'type' => 'Alquileres'])
</div>

<style>
    .tab-btn {
        background: transparent;
        border: none;
        color: rgba(255, 255, 255, 0.6);
        font-weight: 600;
        font-size: 1rem;
        padding: 10px 20px;
        cursor: pointer;
        border-radius: 8px;
        transition: all 0.3s ease;
        border-bottom: 3px solid transparent;
    }
    .tab-btn:hover {
        color: #fff;
        background: rgba(255,255,255,0.05);
    }
    .tab-btn.active {
        color: var(--accent);
        border-bottom-color: var(--accent);
        background: rgba(255,255,255,0.08);
    }
    .tab-content-pane {
        animation: fadeIn 0.4s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    function switchTab(tabName) {
        // Hide all contents
        document.querySelectorAll('.tab-content-pane').forEach(pane => {
            pane.style.display = 'none';
        });
        
        // Remove active class from all buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Show target and activate button
        const targetPane = document.getElementById('tab-content-' + tabName);
        if (targetPane) {
            targetPane.style.display = 'block';
        }
        
        const activeBtn = document.querySelector(`.tab-btn[data-tab="${tabName}"]`);
        if (activeBtn) {
            activeBtn.classList.add('active');
        }
    }

    function toggleRejectionForm(promoId) {
        const form = document.getElementById('rejection-form-' + promoId);
        if (form) {
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
                form.querySelector('textarea').focus();
            } else {
                form.style.display = 'none';
            }
        }
    }
</script>
@endsection