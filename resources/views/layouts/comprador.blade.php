<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TerrenoSur - Catálogo')</title>

    <!-- Tailwind CSS (Carga externa garantizada para el comprador sin afectar el admin) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Desactivar cualquier estilo global que pudiera colarse del admin -->
    <style>
        body, html {
            background-color: #f8fafc !important; /* bg-slate-50 */
            color: #1e293b !important;
            margin: 0;
            padding: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        /* Eliminar variables y fondos oscuros heredados */
        :root {
            --bg-color: #f8fafc !important;
            --text-color: #1e293b !important;
        }
    </style>
</head>
<body class="antialiased bg-slate-50">

    <!-- Navbar Minimalista exclusiva -->
    <nav class="bg-white shadow-sm border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('catalogo.terrenos') }}" class="text-2xl font-black tracking-tighter text-indigo-600 flex items-center gap-2">
                        <i class="fa-solid fa-mountain-sun"></i>
                        TerrenoSur
                    </a>
                </div>

                <!-- Opciones Usuario -->
                <div class="flex items-center gap-4">
                    @auth
                        <div class="text-sm font-medium text-slate-700 bg-slate-100 px-4 py-2 rounded-full border border-slate-200">
                            Hola, <strong>{{ Auth::user()->nombre }}</strong>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="text-sm font-bold text-slate-500 hover:text-red-500 transition-colors">
                                Salir
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800">
                            Iniciar Sesión
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido Inyectado -->
    <main>
        @yield('content')
    </main>

    <!-- Footer Simple -->
    <footer class="bg-white border-t border-slate-200 mt-auto">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm text-slate-500">
                &copy; {{ date('Y') }} TerrenoSur Marketplace. Todos los derechos reservados.
            </p>
        </div>
    </footer>

</body>
</html>
