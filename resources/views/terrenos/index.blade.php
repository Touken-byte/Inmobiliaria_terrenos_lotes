@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Explorar Terrenos</h1>
        <p class="text-gray-600">Encuentra el terreno ideal para tu próximo proyecto.</p>
    </div>

    <!-- Barra de Búsqueda GET -->
    <form action="{{ route('terrenos.index') }}" method="GET" class="mb-10 max-w-2xl">
        <div class="flex items-center bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 transition-all">
            <div class="px-4 text-gray-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="Buscar por descripción o ubicación..."
                class="w-full py-3 pr-4 text-gray-700 bg-transparent outline-none"
            >
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 font-medium transition-colors">
                Buscar
            </button>
        </div>
    </form>

    <!-- Resultados o Empty State -->
    @if($terrenos->isEmpty())
        <!-- ++ EMPTY STATE ++ -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center max-w-2xl mx-auto mt-8">
            <div class="mx-auto h-20 w-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No se encontraron resultados</h3>
            <p class="text-gray-500 mb-6">No encontramos terrenos disponibles que coincidan con "<strong>{{ request('search') }}</strong>".</p>
            <a href="{{ route('terrenos.index') }}" class="inline-block bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-6 py-2.5 rounded-lg transition-colors">
                Limpiar Búsqueda
            </a>
        </div>
    @else
        <!-- ++ GRID DE TERRENOS ++ -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($terrenos as $terreno)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Imagen Portada -->
                    <div class="h-48 bg-gray-100 relative">
                        @if($terreno->imagenes->count() > 0)
                            <img src="{{ asset($terreno->imagenes->first()->ruta_archivo) }}" alt="Terreno" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">
                                Sin imagen
                            </div>
                        @endif
                        <span class="absolute top-3 left-3 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded shadow">
                            Disponible
                        </span>
                    </div>

                    <!-- Datos Terreno -->
                    <div class="p-5">
                        <h3 class="text-lg font-bold text-gray-800 mb-1 truncate">
                            Terreno en {{ $terreno->ubicacion }}
                        </h3>
                        <p class="text-sm text-gray-500 mb-3 truncate flex items-center">
                            <svg class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $terreno->ubicacion }}
                        </p>
                        
                        <p class="text-gray-600 text-sm line-clamp-2 mb-4 h-10">
                            {{ $terreno->descripcion }}
                        </p>

                        <div class="flex justify-between items-end border-t border-gray-100 pt-4 mt-auto">
                            <div class="text-xs text-gray-500">
                                <span>Área:</span> <br>
                                <span class="font-semibold text-gray-700">{{ number_format($terreno->metros_cuadrados, 2) }} m²</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-500 block">Precio:</span>
                                <span class="text-lg font-bold text-blue-600">${{ number_format($terreno->precio, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- ++ PAGINACIÓN ++ -->
        <div class="mt-10">
            {{ $terrenos->links() }}
        </div>
    @endif

</div>
@endsection
