<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mundial Play - Ver en Vivo</title>
    <script src="https://cdn.tailwindcss.com"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        stream: {
                            bg: '#0f1014',
                            card: '#18191f',
                            accent: '#e50914',
                            hover: '#24252e'
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f1014; }
        .live-pulse { animation: pulse-red 2s infinite; }
        @keyframes pulse-red {
            0% { box-shadow: 0 0 0 0 rgba(229, 9, 20, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(229, 9, 20, 0); }
            100% { box-shadow: 0 0 0 0 rgba(229, 9, 20, 0); }
        }
    </style>
</head>
<body class="text-white min-h-screen">

    <nav class="absolute w-full px-6 py-5 flex justify-between items-center z-50 bg-gradient-to-b from-black/80 to-transparent">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-stream-accent rounded flex items-center justify-center font-bold">▶</div>
            <h1 class="font-bold text-xl tracking-tight">CONTI<span class="text-stream-accent">PLAY</span></h1>
        </div>
        <div>
            <a href="/panel-control/1" class="px-4 py-2 text-sm font-semibold bg-white/10 hover:bg-white/20 rounded backdrop-blur-sm transition">Modo Árbitro</a>
        </div>
    </nav>

    <div class="relative h-[60vh] md:h-[70vh] w-full overflow-hidden flex items-end pb-12 md:pb-24 px-6 md:px-16">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1574629810360-7efbbe195018?q=80&w=2693&auto=format&fit=crop')] bg-cover bg-center opacity-40"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#0f1014] via-[#0f1014]/40 to-transparent"></div>

        <div class="relative z-10 max-w-4xl" id="hero-content">
            @if($matches->first())
                @php $first = $matches->first(); @endphp
                <div id="hero-match-{{ $first->id }}" data-match-id="{{ $first->id }}">
                    <span id="hero-badge-{{ $first->id }}" class="inline-block px-3 py-1 mb-4 text-xs font-bold tracking-wider uppercase {{ $first->status == 'live' ? 'bg-red-600 animate-pulse' : 'bg-stream-accent' }} rounded text-white shadow-lg">
                        {{ $first->status == 'live' ? '🔴 EN VIVO AHORA' : 'PARTIDO DESTACADO' }}
                    </span>
                    <h1 class="text-4xl md:text-7xl font-black leading-tight mb-4">
                        {{ $first->team_home }} <span class="text-gray-400 font-thin">vs</span> {{ $first->team_away }}
                    </h1>
                    <div class="text-3xl font-bold mb-4 {{ $first->status == 'live' ? 'block' : 'hidden' }}" id="hero-score-{{ $first->id }}">
                        <span id="hero-score-home-{{ $first->id }}">{{ $first->score_home }}</span> - <span id="hero-score-away-{{ $first->id }}">{{ $first->score_away }}</span>
                    </div>
                    <p class="text-gray-300 text-lg mb-8 max-w-xl line-clamp-2">
                        Transmisión exclusiva desde {{ $first->stadium }}.
                        <span id="hero-status-text-{{ $first->id }}">{{ $first->status == 'live' ? '¡El partido está en curso!' : 'Cobertura completa disponible.' }}</span>
                    </p>
                    <a href="/ver-partido/{{ $first->id }}" class="inline-flex items-center gap-3 px-8 py-4 bg-white text-black font-bold text-lg rounded hover:bg-gray-200 transition transform hover:scale-105">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        VER TRANSMISIÓN
                    </a>
                </div>
            @else
                <h1 class="text-5xl font-bold">Sin partidos programados</h1>
            @endif
        </div>
    </div>

    <div class="px-6 md:px-16 pb-20 -mt-10 relative z-20">
        <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
            <span class="w-1 h-6 bg-stream-accent rounded-full"></span>
            Cartelera de Hoy
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($matches as $match)
                <a href="/ver-partido/{{ $match->id }}" id="card-match-{{ $match->id }}" class="group bg-stream-card rounded-xl overflow-hidden hover:scale-[1.02] transition-all duration-300 shadow-xl border border-white/5 hover:border-white/20 relative">

                    <div class="relative h-48 bg-gray-800 flex items-center justify-center overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-900/40 to-purple-900/40 group-hover:opacity-100 transition-opacity"></div>

                        <div class="relative z-10 flex items-center justify-between w-full px-8">
                            <div class="text-center">
                                <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center text-xl font-bold mb-2 shadow-lg backdrop-blur-md">
                                    {{ substr($match->team_home, 0, 1) }}
                                </div>
                                <span class="text-xs font-bold text-gray-300">{{ substr($match->team_home, 0, 3) }}</span>
                            </div>
                            <span class="text-2xl font-black text-white/20">VS</span>
                            <div class="text-center">
                                <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center text-xl font-bold mb-2 shadow-lg backdrop-blur-md">
                                    {{ substr($match->team_away, 0, 1) }}
                                </div>
                                <span class="text-xs font-bold text-gray-300">{{ substr($match->team_away, 0, 3) }}</span>
                            </div>
                        </div>

                        <div id="badge-container-{{ $match->id }}" class="absolute top-3 left-3">
                            @if($match->status == 'live')
                                <div class="px-2 py-1 bg-red-600 text-[10px] font-bold rounded uppercase flex items-center gap-1 animate-pulse shadow-lg">
                                    <span class="w-1.5 h-1.5 bg-white rounded-full"></span> EN VIVO
                                </div>
                            @else
                                <div class="px-2 py-1 bg-gray-700 text-[10px] font-bold rounded uppercase text-gray-300">
                                    {{ $match->start_time }}
                                </div>
                            @endif
                        </div>

                        <div id="score-container-{{ $match->id }}" class="absolute bottom-3 right-3 text-xs font-mono font-bold bg-black/60 px-2 py-1 rounded backdrop-blur text-white {{ $match->status == 'live' ? '' : 'hidden' }}">
                            <span id="score-home-{{ $match->id }}">{{ $match->score_home }}</span> - <span id="score-away-{{ $match->id }}">{{ $match->score_away }}</span>
                        </div>
                    </div>

                    <div class="p-4">
                        <div class="text-xs text-stream-accent font-bold uppercase mb-1 tracking-wider">{{ $match->league_name }}</div>
                        <h3 class="font-bold text-lg leading-tight mb-1 text-gray-100 group-hover:text-white">{{ $match->team_home }} vs {{ $match->team_away }}</h3>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <script type="module">
        // Obtenemos todos los IDs de los partidos renderizados en PHP
        const matchIds = @json($matches->pluck('id'));

        matchIds.forEach(id => {
            Echo.channel('partido.' + id)
                .listen('.evento.nuevo', (e) => {
                    console.log(`Evento en partido ${id}:`, e);

                    // 1. Actualizar Marcador
                    if(e.type === 'goal_home') updateScore(id, 'home');
                    if(e.type === 'goal_away') updateScore(id, 'away');

                    // 2. Actualizar Estado (Inicio)
                    if(e.type === 'inicio') {
                        setLiveStatus(id, true);
                    }

                    // 3. Actualizar Estado (Fin)
                    if(e.type === 'fin') {
                        setLiveStatus(id, false, 'FINALIZADO');
                    }

                    // 4. Reset
                    if(e.type === 'reset') {
                        resetMatchUI(id);
                    }
                });
        });

        // Funciones de Actualización de UI

        function updateScore(id, team) {
            // Actualizar Tarjeta Pequeña
            const el = document.getElementById(`score-${team}-${id}`);
            if(el) {
                el.innerText = parseInt(el.innerText) + 1;
                // Efecto visual en la tarjeta
                const card = document.getElementById(`card-match-${id}`);
                card.classList.add('ring-2', 'ring-stream-accent');
                setTimeout(() => card.classList.remove('ring-2', 'ring-stream-accent'), 1000);
            }

            // Actualizar Hero (si es el partido destacado)
            const heroEl = document.getElementById(`hero-score-${team}-${id}`);
            if(heroEl) heroEl.innerText = parseInt(heroEl.innerText) + 1;
        }

        function setLiveStatus(id, isLive, text = null) {
            const container = document.getElementById(`badge-container-${id}`);
            const scoreContainer = document.getElementById(`score-container-${id}`);

            // Hero
            const heroBadge = document.getElementById(`hero-badge-${id}`);
            const heroScore = document.getElementById(`hero-score-${id}`);

            if(isLive) {
                // Cambiar a Badge Rojo
                if(container) container.innerHTML = `<div class="px-2 py-1 bg-red-600 text-[10px] font-bold rounded uppercase flex items-center gap-1 animate-pulse shadow-lg"><span class="w-1.5 h-1.5 bg-white rounded-full"></span> EN VIVO</div>`;
                if(scoreContainer) scoreContainer.classList.remove('hidden');

                if(heroBadge) {
                    heroBadge.className = "inline-block px-3 py-1 mb-4 text-xs font-bold tracking-wider uppercase bg-red-600 animate-pulse rounded text-white shadow-lg";
                    heroBadge.innerText = "🔴 EN VIVO AHORA";
                }
                if(heroScore) heroScore.classList.remove('hidden');

            } else {
                // Volver a estado normal o Finalizado
                const label = text || "PROGRAMADO";
                if(container) container.innerHTML = `<div class="px-2 py-1 bg-gray-700 text-[10px] font-bold rounded uppercase text-gray-300">${label}</div>`;

                if(heroBadge) {
                    heroBadge.className = "inline-block px-3 py-1 mb-4 text-xs font-bold tracking-wider uppercase bg-gray-700 rounded text-white shadow-lg";
                    heroBadge.innerText = label;
                }
            }
        }

        function resetMatchUI(id) {
            // Poner ceros
            ['home', 'away'].forEach(team => {
                const el = document.getElementById(`score-${team}-${id}`);
                if(el) el.innerText = "0";
                const heroEl = document.getElementById(`hero-score-${team}-${id}`);
                if(heroEl) heroEl.innerText = "0";
            });

            // Quitar estado live
            setLiveStatus(id, false, "EN ESPERA");

            // Ocultar score container
            const scoreContainer = document.getElementById(`score-container-${id}`);
            if(scoreContainer) scoreContainer.classList.add('hidden');
        }

    </script>
</body>
</html>
