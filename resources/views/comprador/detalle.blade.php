@extends('layouts.comprador')

@section('title', 'Detalles del Terreno | TerrenoSur')

@section('content')
<div class="bg-slate-50 min-h-screen pb-20 pt-8 font-sans text-slate-800">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- HEADER / VOLVER -->
        <a href="{{ route('catalogo.terrenos') }}" class="inline-flex items-center text-sm font-bold text-slate-500 hover:text-blue-600 transition-colors mb-6 group">
            <i class="fa-solid fa-arrow-left mr-2 bg-white p-2 rounded-full shadow-sm border border-slate-200 group-hover:-translate-x-1 transition-transform"></i>
            Volver al catálogo
        </a>

        <!-- MAIN CARD -->
        <div class="bg-white rounded-[24px] shadow-sm border border-slate-200 overflow-hidden flex flex-col md:flex-row">
            
            <!-- GALERÍA / IMAGEN GRANDE -->
            <div class="w-full md:w-1/2 lg:w-3/5 bg-slate-100 relative h-80 md:h-[500px]">
                @if($terreno->imagenes->count() > 0)
                    <img src="{{ asset($terreno->imagenes->first()->ruta_archivo) }}" alt="Terreno en {{ $terreno->ubicacion }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-200/50">
                        <i class="fa-regular fa-images text-5xl mb-3 opacity-50"></i>
                        <span class="text-sm font-black uppercase tracking-widest">Sin foto</span>
                    </div>
                @endif
                
                <!-- BADGES SOBRE IMAGEN -->
                <div class="absolute top-4 left-4 flex gap-2">
                    <span class="bg-emerald-500/90 backdrop-blur text-white text-xs font-black uppercase tracking-wider px-4 py-2 rounded-full shadow-md">
                        En Venta
                    </span>
                </div>
            </div>

            <!-- DETALLES Y CTA -->
            <div class="w-full md:w-1/2 lg:w-2/5 p-8 flex flex-col relative">
                
                <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-2 border-b border-slate-100 pb-2">Información de la Propiedad</p>

                <!-- Ubicación -->
                <h1 class="text-3xl font-black text-slate-900 leading-tight mb-2">
                    Lote en {{ Str::words($terreno->ubicacion, 4, '') }}
                </h1>
                <div class="flex items-center gap-2 mb-6">
                    <i class="fa-solid fa-location-dot text-blue-500"></i>
                    <h2 class="text-base text-slate-500 font-medium">{{ $terreno->ubicacion }}</h2>
                </div>
                
                <!-- Precio Destacado -->
                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 mb-6 flex flex-col">
                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Precio Total</span>
                    <div class="flex items-baseline">
                        <span class="text-4xl font-black text-blue-600 tracking-tighter">${{ number_format($terreno->precio, 2) }}</span>
                        <span class="text-sm font-bold text-slate-500 ml-2">USD</span>
                    </div>
                </div>

                <!-- Metros y Características -->
                <div class="flex gap-4 mb-6">
                    <div class="flex-1 bg-white border border-slate-200 rounded-xl p-4 flex flex-col items-center justify-center shadow-sm">
                        <i class="fa-solid fa-vector-square text-xl text-slate-400 mb-1"></i>
                        <span class="text-sm text-slate-500 mb-0.5">Superficie</span>
                        <span class="text-lg font-black text-slate-800">{{ number_format($terreno->metros_cuadrados, 0) }} m²</span>
                    </div>
                    <div class="flex-1 bg-white border border-slate-200 rounded-xl p-4 flex flex-col items-center justify-center shadow-sm">
                        <i class="fa-solid fa-map text-xl text-slate-400 mb-1"></i>
                        <span class="text-sm text-slate-500 mb-0.5">Tipo</span>
                        <span class="text-lg font-black text-slate-800">Terreno Urbano</span>
                    </div>
                </div>

                <!-- Descripción Completa -->
                <div class="mb-8 flex-grow">
                    <h3 class="text-base font-bold text-slate-800 mb-2">Descripción del lugar</h3>
                    <p class="text-slate-600 leading-relaxed text-[15px]">
                        {{ $terreno->descripcion }}
                    </p>
                </div>

                <!-- CALL TO ACTION -->
                <div class="mt-auto">
                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl transition-all duration-300 text-lg flex items-center justify-center gap-3 shadow-lg shadow-blue-600/30">
                        Contactar Inmobiliaria
                        <i class="fa-brands fa-whatsapp text-xl"></i>
                    </button>
                    <p class="text-center text-xs text-slate-400 mt-3">* Funcionalidad de contacto próximamente</p>
                </div>

            </div>
        </div>
        
    </div>
</div>
@endsection
