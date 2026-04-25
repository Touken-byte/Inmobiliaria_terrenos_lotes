@extends('layouts.comprador')

@section('title', 'Catálogo de Terrenos | TerrenoSur')

@section('content')
<div class="bg-slate-50 min-h-screen pb-20 font-sans text-slate-800">
    
    <!-- 1. BUSCADOR (Header pegajoso) -->
    <div class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-5">
            <div class="flex justify-center w-full">
                <form action="{{ route('catalogo.terrenos') }}" method="GET" class="w-full max-w-3xl">
                    <div class="flex items-center w-full bg-white rounded-full shadow hover:shadow-md border border-slate-300 focus-within:ring-4 focus-within:ring-blue-100 focus-within:border-blue-500 transition-all p-1.5 h-14 sm:h-16 relative overflow-hidden">
                        
                        <div class="pl-5 pr-3 text-slate-400">
                            <i class="fa-solid fa-search text-lg"></i>
                        </div>
                        
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="Zonas, barrio o características clave..."
                            class="flex-1 w-full bg-transparent px-2 py-2 text-slate-800 outline-none text-base sm:text-lg font-medium placeholder-slate-400"
                        >
                        
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold h-full px-6 sm:px-8 rounded-full transition-colors flex items-center shadow-md">
                            <span class="hidden sm:inline">Buscar</span>
                            <i class="fa-solid fa-search sm:hidden"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MAIN GRID CONTAINER -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- ============================================== -->
            <!-- COLUMNA IZQUIERDA: FILTROS Y MAPA (1 Columna)  -->
            <!-- ============================================== -->
            <div class="w-full lg:w-1/4 flex flex-col gap-6 flex-shrink-0">
                
                <!-- 3. MAPA (Placeholder IN-C02) -->
                <div class="bg-slate-200 rounded-2xl border border-slate-300 h-64 flex flex-col items-center justify-center text-slate-500 shadow-sm relative overflow-hidden group">
                    <!-- Textura visual simulando un mapa de fondo -->
                    <div class="absolute inset-0 opacity-10 bg-[url('https://maps.wikimedia.org/osm-intl/11/1075/1335.png')] bg-cover bg-center"></div>
                    <i class="fa-solid fa-map-location-dot text-4xl mb-3 text-slate-400 group-hover:scale-110 transition-transform"></i>
                    <h3 class="font-bold text-slate-700 mb-1">Vista en Mapa</h3>
                    <p class="text-xs text-center px-4 font-medium uppercase tracking-wider text-slate-500">Próximamente (IN-C02)</p>
                </div>

                <!-- 2. FILTROS (Placeholder IN-C01) -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-black text-lg text-slate-900 border-b-2 border-blue-600 pb-1 inline-block">Filtros</h3>
                    </div>
                    
                    <div class="space-y-6 opacity-60 pointer-events-none select-none">
                        <!-- Rango de Precio -->
                        <div>
                            <h4 class="font-bold text-sm text-slate-700 mb-3 uppercase tracking-wider">Rango de Precio</h4>
                            <div class="flex items-center gap-2">
                                <div class="bg-slate-100 rounded-lg p-2 text-center text-xs flex-1 border border-slate-200">Mínimo</div>
                                <span class="text-slate-400">-</span>
                                <div class="bg-slate-100 rounded-lg p-2 text-center text-xs flex-1 border border-slate-200">Máximo</div>
                            </div>
                        </div>

                        <!-- Tipo de Inmueble -->
                        <div class="border-t border-slate-100 pt-5">
                            <h4 class="font-bold text-sm text-slate-700 mb-3 uppercase tracking-wider">Disponibilidad</h4>
                            <label class="flex items-center gap-3 mb-2 cursor-pointer">
                                <div class="w-4 h-4 rounded border border-slate-300 bg-blue-500 border-none flex items-center justify-center"><i class="fa-solid fa-check text-[10px] text-white"></i></div>
                                <span class="text-sm">Venta de Terrenos</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <div class="w-4 h-4 rounded border border-slate-300"></div>
                                <span class="text-sm">Alquiler de Cuartos <span class="text-[10px] bg-amber-100 text-amber-700 font-bold px-2 py-0.5 rounded-full ml-1">(IN-R02)</span></span>
                            </label>
                        </div>
                        
                        <!-- Superficie -->
                        <div class="border-t border-slate-100 pt-5">
                            <h4 class="font-bold text-sm text-slate-700 mb-3 uppercase tracking-wider">Servicios Básicos</h4>
                            <div class="flex flex-wrap gap-2">
                                <span class="text-xs bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-full">Agua</span>
                                <span class="text-xs bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-full">Luz</span>
                                <span class="text-xs bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-full">Alcantarillado</span>
                            </div>
                        </div>
                    </div>

                    <!-- Botón falso filtros -->
                    <button class="w-full mt-6 bg-slate-100 text-slate-400 font-bold py-3 rounded-xl border border-slate-200 border-dashed text-sm uppercase tracking-widest cursor-not-allowed">
                        Próximamente (IN-C01)
                    </button>
                </div>

            </div>

            <!-- ============================================== -->
            <!-- COLUMNA DERECHA: RESULTADOS (3 Columnas)       -->
            <!-- ============================================== -->
            <div class="w-full lg:w-3/4">
                
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-6 pb-4 border-b border-slate-200">
                    <div>
                        @if(request('search'))
                            <h2 class="text-2xl font-black text-slate-900 mb-1">Buscando: "<span class="text-blue-600">{{ request('search') }}</span>"</h2>
                            <p class="text-sm text-slate-500 font-medium line-clamp-1">Explora las opciones de terrenos que coinciden con tu búsqueda.</p>
                        @else
                            <h2 class="text-2xl font-black text-slate-900 mb-1">Terrenos Disponibles</h2>
                            <p class="text-sm text-slate-500 font-medium">Descubre terrenos con alto potencial cerca de ti.</p>
                        @endif
                    </div>
                    <div class="mt-3 sm:mt-0 font-bold text-sm bg-white border border-slate-200 shadow-sm px-4 py-2 rounded-full whitespace-nowrap">
                        {{ $terrenos->total() }} <span class="text-slate-500 font-medium">Resultados</span>
                    </div>
                </div>

                @if($terrenos->isEmpty())
                    <!-- 6. EMPTY STATE UI -->
                    <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-12 text-center mt-6">
                        <div class="mx-auto h-20 w-20 bg-blue-50 rounded-full flex items-center justify-center mb-5">
                            <i class="fa-solid fa-magnifying-glass-location text-3xl text-blue-500"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-2">Sin resultados</h3>
                        <p class="text-slate-500 mb-8 max-w-md mx-auto text-base">No logramos encontrar propiedades que coincidan exacto con tu búsqueda. Intenta probar nombres más amplios.</p>
                        <a href="{{ route('catalogo.terrenos') }}" class="inline-flex items-center justify-center px-8 py-3 border border-slate-200 shadow-sm text-sm font-bold uppercase tracking-wider rounded-full text-slate-700 bg-white hover:bg-slate-50 hover:text-blue-600 transition-colors">
                            Limpiar Búsqueda
                        </a>
                    </div>
                @else
                    <!-- 5. GRID DE CARDS MARKETPLACE -->
                    <!-- Grid Responsive: 1 movil, 2 tablet, 3 desktop -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7">
                        @foreach($terrenos as $terreno)
                            <div class="group bg-white rounded-[20px] shadow-sm hover:shadow-2xl border border-slate-200 hover:border-transparent overflow-hidden hover:-translate-y-1.5 transition-all duration-300 flex flex-col h-full cursor-pointer relative">
                                
                                <!-- IMAGEN HEADER -->
                                <div class="relative w-full h-[220px] bg-slate-100 overflow-hidden">
                                    @if($terreno->imagenes->count() > 0)
                                        <img src="{{ asset($terreno->imagenes->first()->ruta_archivo) }}" alt="Terreno en {{ $terreno->ubicacion }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-in-out">
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-200/50">
                                            <i class="fa-regular fa-images text-4xl mb-2 opacity-50"></i>
                                            <span class="text-[10px] font-black uppercase tracking-widest">Sin foto</span>
                                        </div>
                                    @endif

                                    <!-- BADGES SUPERIORES FLOTANTES -->
                                    <div class="absolute top-4 left-4 flex gap-2 z-10">
                                        <span class="bg-white/95 backdrop-blur shadow-md text-slate-900 text-xs font-black px-3.5 py-1.5 rounded-full tracking-wide">
                                            {{ number_format($terreno->metros_cuadrados, 0) }} m²
                                        </span>
                                    </div>
                                    <div class="absolute top-4 right-4 z-10">
                                        <span class="bg-emerald-500/90 backdrop-blur text-white text-[10px] font-black uppercase tracking-wider px-3 py-1.5 rounded-full shadow-md">
                                            Venta
                                        </span>
                                    </div>
                                    <!-- Degradado oscurecedor abajo para que texto resalte si usamos overlay future proof -->
                                    <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/40 to-transparent"></div>
                                </div>

                                <!-- INFORMACIÓN TARJETA -->
                                <div class="px-5 pt-4 pb-5 flex flex-col flex-grow">
                                    
                                    <!-- Precio Destacado (Marketplace prioritiza el precio) -->
                                    <div class="mb-3">
                                        <h4 class="text-2xl font-black text-blue-600 tracking-tight flex items-baseline">
                                            ${{ number_format($terreno->precio, 2) }} <span class="text-xs font-medium text-slate-400 ml-1 tracking-normal">USD</span>
                                        </h4>
                                    </div>

                                    <!-- Ubicación -->
                                    <h3 class="text-base font-bold text-slate-800 group-hover:text-blue-700 transition-colors line-clamp-1 mb-1">
                                        Lote en {{ Str::words($terreno->ubicacion, 4, '') }}
                                    </h3>
                                    
                                    <!-- Sub Ubicación -->
                                    <div class="flex items-start gap-1.5 mt-1.5 mb-3">
                                        <i class="fa-solid fa-location-dot text-slate-400 mt-1text-sm"></i>
                                        <p class="text-[13px] text-slate-500 font-medium leading-tight line-clamp-1">
                                            {{ $terreno->ubicacion }}
                                        </p>
                                    </div>
                                    
                                    <!-- Descripción Corta -->
                                    <p class="text-sm text-slate-600 line-clamp-2 leading-relaxed mb-5 flex-grow">
                                        {{ $terreno->descripcion }}
                                    </p>

                                    <!-- BOTÓN DE VER DETALLES -->
                                    <div class="border-t border-slate-100 pt-4 mt-auto">
                                        <a href="{{ route('catalogo.detalle', $terreno->id) }}" class="w-full bg-slate-50 group-hover:bg-blue-600 text-slate-600 group-hover:text-white border border-slate-200 group-hover:border-blue-600 font-bold py-2.5 rounded-xl transition-all duration-300 text-sm flex items-center justify-center gap-2 shadow-sm relative overflow-hidden">
                                            Ver detalles
                                            <i class="fa-solid fa-arrow-right-long opacity-0 -translate-x-4 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300 absolute right-4"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- PAGINACIÓN -->
                    <div class="mt-14 mb-4 flex justify-center">
                        <div class="bg-white px-4 py-2 rounded-2xl shadow-sm border border-slate-100">
                            {{ $terrenos->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

<style>
/* Ajustar estilos menores de la paginación nativa dentro del contenedor para que resalte y no rompa bordes */
nav[role="navigation"] { display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
nav[role="navigation"] a, nav[role="navigation"] span[aria-hidden] { border-radius: 9999px !important; }
</style>
@endsection
