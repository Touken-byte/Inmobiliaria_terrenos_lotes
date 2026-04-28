@extends('layouts.comprador')

@section('title', 'Descubre tu próximo terreno | TerrenoSur Premium')

@section('content')
<div class="pb-24 w-full">
    
    <!-- Hero Header / Buscador -->
    <div class="relative w-full pt-16 pb-20 px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center text-center overflow-hidden">
        <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-6 tracking-tight animate-fade-in-up" style="opacity: 0; animation-delay: 0.1s;">
            Encuentra el terreno <br/><span class="text-gradient">de tus sueños</span>
        </h1>
        <p class="text-lg md:text-xl text-slate-400 max-w-2xl mb-12 font-light animate-fade-in-up leading-relaxed" style="opacity: 0; animation-delay: 0.2s;">
            Explora la colección más exclusiva de propiedades con alto potencial de plusvalía, verificadas y listas para invertir.
        </p>

        <form action="{{ route('catalogo.terrenos') }}" method="GET" class="w-full max-w-3xl z-10 animate-fade-in-up" style="opacity: 0; animation-delay: 0.3s;">
            <div class="glass-panel rounded-full p-2.5 flex items-center shadow-2xl shadow-brand-500/10 focus-within:shadow-brand-500/20 focus-within:border-brand-500/50 transition-all duration-300">
                <div class="pl-6 pr-3 text-brand-400">
                    <i class="fa-solid fa-location-crosshairs text-2xl"></i>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Busca zonas, características o palabras clave..."
                    class="flex-1 w-full bg-transparent px-2 py-3 text-white outline-none text-lg font-medium placeholder-slate-500"
                >
                <button type="submit" class="bg-white text-darker hover:bg-slate-200 font-extrabold h-full px-8 py-3.5 rounded-full transition-colors flex items-center shadow-md">
                    <span class="tracking-wide">Explorar</span>
                </button>
            </div>
        </form>
    </div>

    <!-- MAIN GRID CONTAINER -->
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 mt-6 animate-fade-in-up" style="opacity: 0; animation-delay: 0.4s;">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- COLUMNA IZQUIERDA: FILTROS Y MAPA -->
            <div class="w-full lg:w-[320px] flex flex-col gap-6 flex-shrink-0">
                
                <!-- MAPA -->
                <div class="glass-card rounded-3xl h-[300px] flex flex-col items-center justify-center text-slate-400 relative overflow-hidden group">
                    <div class="absolute inset-0 opacity-30 bg-[url('https://maps.wikimedia.org/osm-intl/11/1075/1335.png')] bg-cover bg-center filter grayscale contrast-150 mix-blend-overlay group-hover:scale-110 transition-transform duration-1000"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-dark/90 to-dark/40"></div>
                    
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-20 h-20 rounded-full bg-brand-500/20 flex items-center justify-center mb-5 backdrop-blur-md border border-brand-500/30 group-hover:bg-brand-500/40 group-hover:scale-110 transition-all duration-500 shadow-[0_0_30px_rgba(20,184,166,0.3)]">
                            <i class="fa-solid fa-map-location-dot text-3xl text-brand-400"></i>
                        </div>
                        <h3 class="font-bold text-white text-xl mb-2">Vista Dinámica 3D</h3>
                        <p class="text-[10px] font-black uppercase tracking-[0.25em] text-brand-400 bg-brand-500/10 px-4 py-1.5 rounded-full border border-brand-500/20">Próximamente (IN-C02)</p>
                    </div>
                </div>

                <!-- FILTROS -->
                <div class="glass-card rounded-3xl p-7">
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="font-bold text-xl text-white flex items-center gap-3">
                            <i class="fa-solid fa-sliders text-brand-400"></i> Inteligencia Inmobiliaria
                        </h3>
                    </div>
                    
                    <div class="space-y-8 opacity-40 pointer-events-none select-none relative">
                        <div class="absolute inset-0 z-10"></div> <!-- Bloqueador de clicks -->
                        
                        <!-- Rango de Precio -->
                        <div>
                            <h4 class="font-semibold text-[11px] text-slate-400 mb-4 uppercase tracking-widest">Rango de Inversión</h4>
                            <div class="flex items-center gap-3">
                                <div class="bg-dark/60 rounded-xl p-3.5 text-center text-sm flex-1 border border-white/5 text-slate-300 shadow-inner">Min USD</div>
                                <span class="text-slate-600">-</span>
                                <div class="bg-dark/60 rounded-xl p-3.5 text-center text-sm flex-1 border border-white/5 text-slate-300 shadow-inner">Max USD</div>
                            </div>
                        </div>

                        <!-- Tipo de Inmueble -->
                        <div class="border-t border-white/5 pt-7">
                            <h4 class="font-semibold text-[11px] text-slate-400 mb-4 uppercase tracking-widest">Tipo de Propiedad</h4>
                            <label class="flex items-center gap-4 mb-4">
                                <div class="w-5 h-5 rounded-md border border-brand-500 bg-brand-500 flex items-center justify-center shadow-[0_0_10px_rgba(20,184,166,0.4)]"><i class="fa-solid fa-check text-xs text-white"></i></div>
                                <span class="text-white font-medium">Terrenos Exclusivos</span>
                            </label>
                            <label class="flex items-center gap-4">
                                <div class="w-5 h-5 rounded-md border border-slate-600 bg-dark/50"></div>
                                <span class="text-slate-400">Locales Comerciales</span>
                            </label>
                        </div>
                    </div>

                    <button class="w-full mt-8 bg-gradient-to-r from-white/5 to-white/10 text-slate-300 font-bold py-4 rounded-xl border border-white/10 hover:bg-white/10 transition-colors text-xs uppercase tracking-widest cursor-not-allowed">
                        Filtros Predictivos (Pronto)
                    </button>
                </div>
            </div>

            <!-- COLUMNA DERECHA: RESULTADOS -->
            <div class="w-full flex-1">
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 pb-6 border-b border-white/10 gap-4">
                    <div>
                        @if(request('search'))
                            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-2 tracking-tight">Buscando: <span class="text-brand-400">"{{ request('search') }}"</span></h2>
                            <p class="text-slate-400 font-light text-lg">Algoritmo de búsqueda encontró estas coincidencias.</p>
                        @else
                            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-2 tracking-tight">Catálogo Premium</h2>
                            <p class="text-slate-400 font-light text-lg">Terrenos de alta plusvalía listos para invertir.</p>
                        @endif
                    </div>
                    <div class="font-medium text-sm glass-panel px-6 py-3 rounded-full text-slate-300 flex items-center gap-3 border-white/10 shadow-lg">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-brand-500 shadow-[0_0_10px_rgba(45,212,191,1)]"></span>
                        </span>
                        <strong class="text-white text-xl">{{ $terrenos->total() }}</strong> <span class="uppercase tracking-widest text-xs opacity-70">Encontrados</span>
                    </div>
                </div>

                @if($terrenos->isEmpty())
                    <!-- EMPTY STATE -->
                    <div class="glass-card rounded-[2rem] p-20 text-center mt-6 flex flex-col items-center border-dashed border-white/20">
                        <div class="w-28 h-28 bg-dark/60 rounded-[2rem] flex items-center justify-center mb-8 shadow-inner border border-white/5 relative">
                            <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full"></div>
                            <i class="fa-solid fa-satellite-dish text-5xl text-brand-400 relative z-10 animate-pulse"></i>
                        </div>
                        <h3 class="text-4xl font-extrabold text-white mb-4 tracking-tight">Sin señales en el radar</h3>
                        <p class="text-slate-400 mb-10 max-w-lg text-lg font-light leading-relaxed">Nuestra red neuronal no encontró propiedades que coincidan exactamente con esos parámetros. Intenta ampliar tu rango.</p>
                        <a href="{{ route('catalogo.terrenos') }}" class="inline-flex items-center justify-center px-10 py-4 rounded-full text-sm font-extrabold uppercase tracking-widest text-darker bg-white hover:bg-brand-50 hover:shadow-[0_0_30px_rgba(255,255,255,0.4)] transition-all duration-300 hover:-translate-y-1">
                            Restablecer Radar
                        </a>
                    </div>
                @else
                    <!-- GRID DE CARDS MARKETPLACE PREMIUM -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                        @foreach($terrenos as $terreno)
                            <div class="glass-card rounded-3xl overflow-hidden group cursor-pointer flex flex-col h-full relative" onclick="window.location.href='{{ route('catalogo.detalle', $terreno->id) }}'">
                                
                                <!-- IMAGEN HEADER -->
                                <div class="relative w-full aspect-[4/3] bg-darker overflow-hidden">
                                    @if($terreno->imagenes->count() > 0)
                                        <img src="{{ asset($terreno->imagenes->first()->ruta_archivo) }}" alt="Terreno" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-[1.5s] ease-out opacity-80 group-hover:opacity-100">
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-600 bg-dark/80 relative">
                                            <i class="fa-solid fa-gem text-5xl mb-4 opacity-20"></i>
                                            <span class="text-xs font-bold uppercase tracking-[0.3em] opacity-40">Propiedad Verificada</span>
                                        </div>
                                    @endif

                                    <!-- Badges -->
                                    <div class="absolute top-5 left-5 flex gap-2 z-10">
                                        <span class="glass-panel text-white text-xs font-bold px-4 py-2 rounded-full tracking-wider border-white/20 shadow-lg backdrop-blur-md flex items-center gap-1.5">
                                            <i class="fa-solid fa-vector-square text-brand-400"></i> {{ number_format($terreno->metros_cuadrados, 0) }} m²
                                        </span>
                                    </div>
                                    <div class="absolute top-5 right-5 z-10">
                                        <span class="bg-brand-500 text-darker text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-full shadow-[0_0_20px_rgba(20,184,166,0.6)]">
                                            Venta
                                        </span>
                                    </div>

                                    <!-- Gradiente inferior -->
                                    <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-dark to-transparent opacity-95"></div>
                                </div>

                                <!-- INFORMACIÓN TARJETA -->
                                <div class="p-7 flex flex-col flex-grow relative z-10 bg-gradient-to-b from-dark/40 to-transparent -mt-10">
                                    
                                    <!-- Precio Destacado -->
                                    <div class="mb-4">
                                        <h4 class="text-3xl font-extrabold text-white tracking-tight flex items-baseline gap-1 drop-shadow-lg">
                                            <span class="text-brand-400 font-medium">$</span>{{ number_format($terreno->precio, 2) }} <span class="text-[11px] font-medium text-slate-400 uppercase tracking-widest ml-1">USD</span>
                                        </h4>
                                    </div>

                                    <!-- Ubicación -->
                                    <h3 class="text-xl font-bold text-slate-100 group-hover:text-brand-300 transition-colors line-clamp-1 mb-2 tracking-tight">
                                        {{ Str::words($terreno->ubicacion, 5, '...') }}
                                    </h3>
                                    
                                    <!-- Descripción Corta -->
                                    <p class="text-sm text-slate-400 line-clamp-2 leading-relaxed mb-8 flex-grow font-light">
                                        {{ $terreno->descripcion }}
                                    </p>

                                    <!-- BOTÓN DE VER DETALLES -->
                                    <div class="mt-auto">
                                        <span class="w-full bg-white/5 border border-white/10 group-hover:border-brand-500/50 group-hover:bg-brand-500/10 text-white font-semibold py-3.5 rounded-xl transition-all duration-300 text-sm flex items-center justify-between px-6 shadow-inner relative overflow-hidden">
                                            <span class="relative z-10">Explorar Propiedad</span>
                                            <i class="fa-solid fa-arrow-right text-brand-400 group-hover:translate-x-2 transition-transform relative z-10"></i>
                                            <div class="absolute inset-0 bg-gradient-to-r from-brand-500/0 via-brand-500/10 to-brand-500/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- PAGINACIÓN -->
                    <div class="mt-20 flex justify-center pb-10">
                        <div class="glass-panel px-6 py-3 rounded-full border border-white/10 shadow-2xl">
                            {{ $terrenos->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
/* Reset de paginación para tema oscuro premium */
nav[role="navigation"] { display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
nav[role="navigation"] a, nav[role="navigation"] span[aria-hidden] { 
    border-radius: 9999px !important; 
    background: transparent !important;
    border-color: rgba(255,255,255,0.05) !important;
    color: #94a3b8 !important;
    font-weight: 500 !important;
    transition: all 0.3s ease !important;
}
nav[role="navigation"] span[aria-current="page"] span {
    background: #14b8a6 !important; /* brand-500 */
    border-color: #14b8a6 !important;
    color: #06080D !important;
    font-weight: 800 !important;
    border-radius: 9999px !important;
    box-shadow: 0 0 15px rgba(20, 184, 166, 0.4) !important;
}
nav[role="navigation"] a:hover {
    background: rgba(255,255,255,0.1) !important;
    color: white !important;
    border-color: rgba(255,255,255,0.2) !important;
}
nav[role="navigation"] svg {
    width: 1.25rem !important;
    height: 1.25rem !important;
}
</style>
@endsection
