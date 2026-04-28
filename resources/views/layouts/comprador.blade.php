<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TerrenoSur - Catálogo Premium')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#f0fdfa', 100: '#ccfbf1', 200: '#99f6e4', 300: '#5eead4', 400: '#2dd4bf',
                            500: '#14b8a6', 600: '#0d9488', 700: '#0f766e', 800: '#115e59', 900: '#134e4a',
                        },
                        dark: '#0B0F19',
                        darker: '#06080D',
                        card: 'rgba(20, 25, 40, 0.7)'
                    },
                    animation: {
                        'blob': 'blob 10s infinite alternate',
                        'fade-in-up': 'fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        },
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #0B0F19; color: #e2e8f0; overflow-x: hidden; }
        .glass-panel {
            background: rgba(17, 24, 39, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-card {
            background: linear-gradient(145deg, rgba(30, 41, 59, 0.7) 0%, rgba(15, 23, 42, 0.8) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            transform: translateY(-8px);
            border-color: rgba(45, 212, 191, 0.3); /* brand-400 */
            box-shadow: 0 20px 40px -10px rgba(45, 212, 191, 0.15);
        }
        .text-gradient {
            background: linear-gradient(to right, #2dd4bf, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .bg-gradient-hover {
            background: linear-gradient(to right, #0d9488, #2563eb);
            background-size: 200% auto;
            transition: 0.5s;
        }
        .bg-gradient-hover:hover { background-position: right center; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0B0F19; }
        ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #334155; }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col relative">

    <!-- Background glowing orbs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-[-1]">
        <div class="absolute top-[-10%] left-[-10%] w-[30rem] h-[30rem] bg-brand-600/20 rounded-full mix-blend-screen filter blur-[100px] animate-blob"></div>
        <div class="absolute top-[20%] right-[-10%] w-[35rem] h-[35rem] bg-blue-600/15 rounded-full mix-blend-screen filter blur-[120px] animate-blob" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-[-20%] left-[20%] w-[45rem] h-[45rem] bg-indigo-600/10 rounded-full mix-blend-screen filter blur-[130px] animate-blob" style="animation-delay: 4s;"></div>
    </div>

    <!-- Navbar -->
    <nav class="glass-panel sticky top-0 z-50">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('catalogo.terrenos') }}" class="text-2xl font-extrabold tracking-tight flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-400 to-blue-600 flex items-center justify-center text-white shadow-lg shadow-brand-500/30 group-hover:shadow-brand-500/50 transition-all duration-300 transform group-hover:scale-105">
                            <i class="fa-solid fa-mountain-sun"></i>
                        </div>
                        <span class="text-white">Terreno<span class="text-gradient">Sur</span></span>
                    </a>
                </div>

                <div class="flex items-center gap-5">
                    @auth
                        <div class="hidden sm:flex flex-col items-end">
                            <span class="text-[10px] text-slate-400 uppercase tracking-widest font-semibold">Inversor Verificado</span>
                            <span class="text-sm font-bold text-white">{{ Auth::user()->nombre }}</span>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-brand-400 font-bold shadow-inner">
                            {{ strtoupper(substr(Auth::user()->nombre, 0, 1)) }}
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="m-0 ml-2">
                            @csrf
                            <button type="submit" class="text-slate-400 hover:text-red-400 transition-colors p-2 rounded-lg hover:bg-red-500/10" title="Cerrar sesión">
                                <i class="fa-solid fa-power-off"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-7 py-2.5 rounded-full text-sm font-bold text-white bg-gradient-hover shadow-lg shadow-blue-500/25 transition-transform hover:scale-105">
                            Acceder
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1 w-full">
        @yield('content')
    </main>

    <footer class="glass-panel mt-auto border-t-0 border-t border-white/5">
        <div class="max-w-[1400px] mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-2 text-xl font-bold text-white opacity-40">
                    <i class="fa-solid fa-mountain-sun text-brand-500"></i> TerrenoSur
                </div>
                <p class="text-center text-sm text-slate-500 font-medium">
                    &copy; {{ date('Y') }} TerrenoSur Marketplace Premium. Diseñado para el futuro.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
