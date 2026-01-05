<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sala de Control | Producción</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        control: {
                            bg: '#0b0c10',       // Fondo Negro Profundo
                            panel: '#15161a',    // Fondo Paneles
                            surface: '#1e2026',  // Superficie botones
                            accent: '#3b82f6',   // Azul neón
                            danger: '#ef4444',   // Rojo alerta
                            success: '#10b981',  // Verde activo
                            warning: '#f59e0b'   // Amarillo
                        }
                    },
                    fontFamily: {
                        mono: ['Menlo', 'Monaco', 'Courier New', 'monospace'],
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #0b0c10; font-family: 'Inter', sans-serif; }

        /* Efecto Led Brillante */
        .led-on { box-shadow: 0 0 10px currentColor; }

        /* Botones estilo Switcher */
        .btn-control {
            transition: all 0.1s;
            border-bottom: 4px solid rgba(0,0,0,0.3);
        }
        .btn-control:active {
            transform: translateY(2px);
            border-bottom: 0px solid transparent;
        }

        /* Scrollbar consola */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #15161a; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 3px; }
    </style>
</head>
<body class="text-gray-300 min-h-screen flex flex-col md:flex-row">

    <div class="w-full md:w-64 bg-control-panel border-r border-white/5 flex flex-col shrink-0">
        <div class="p-6 border-b border-white/5">
            <h1 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">PRODUCCIÓN</h1>
            <div class="font-black text-xl text-white tracking-tight flex items-center gap-2">
                <span class="w-3 h-3 bg-control-danger rounded-full animate-pulse"></span>
                CONTROL ROOM
            </div>
        </div>

        <div class="p-6 flex-1 space-y-6">
            <div>
                <div class="text-[10px] font-bold text-gray-500 uppercase mb-2">Estado de Transmisión</div>
                <div id="status-display" class="bg-black/50 border border-white/10 rounded p-3 text-center">
                    <div id="status-indicator-text" class="text-sm font-black text-gray-400">OFF AIR</div>
                </div>
            </div>

            <div>
                <div class="text-[10px] font-bold text-gray-500 uppercase mb-2">Meta Datos</div>
                <div class="space-y-2 text-xs font-mono">
                    <div class="flex justify-between">
                        <span class="text-gray-500">ID:</span>
                        <span class="text-control-accent">#{{ $match->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Liga:</span>
                        <span class="text-white text-right">{{ $match->league_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Estadio:</span>
                        <span class="text-white text-right">{{ $match->stadium }}</span>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-white/5">
                <a href="/" class="block w-full py-2 text-xs text-center border border-white/10 rounded hover:bg-white/5 transition mb-2">
                    ← Volver al Inicio
                </a>
                <a href="/ver-partido/{{ $match->id }}" target="_blank" class="block w-full py-2 text-xs text-center bg-control-accent/10 text-control-accent border border-control-accent/20 rounded hover:bg-control-accent/20 transition">
                    Ver Transmisión ↗
                </a>
            </div>
        </div>
    </div>

    <div class="flex-1 flex flex-col max-h-screen overflow-hidden">

        <div class="h-48 bg-black relative border-b border-white/10 flex items-center justify-center shrink-0">
            <div class="bg-control-panel/90 backdrop-blur border border-white/10 rounded-lg p-4 flex items-center gap-8 shadow-2xl">
                <div class="text-center">
                    <h2 class="text-2xl font-black text-white">{{ $match->team_home }}</h2>
                    <div class="text-xs text-gray-500 uppercase tracking-widest">Local</div>
                </div>
                <div class="bg-black px-6 py-2 rounded border border-white/20 font-mono text-4xl font-bold text-control-accent tracking-widest">
                    <span id="score-home-display">{{ $match->score_home }}</span>
                    <span class="text-gray-600 mx-2">:</span>
                    <span id="score-away-display">{{ $match->score_away }}</span>
                </div>
                <div class="text-center">
                    <h2 class="text-2xl font-black text-white">{{ $match->team_away }}</h2>
                    <div class="text-xs text-gray-500 uppercase tracking-widest">Visita</div>
                </div>
            </div>

            <div class="absolute top-4 right-4 font-mono text-xl font-bold text-red-500 flex items-center gap-2 bg-black/80 px-3 py-1 rounded">
                <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                <span id="timer-display">{{ $currentMinute }}'</span>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 md:p-8 bg-control-bg">
            <div class="max-w-5xl mx-auto space-y-8">

                <div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 border-l-2 border-white/20 pl-2">Control de Tiempo</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <button onclick="triggerEvent('inicio')" class="btn-control h-16 bg-control-surface hover:bg-gray-700 rounded text-white font-bold flex flex-col items-center justify-center border-t-4 border-control-success">
                            <span class="text-lg">▶ INICIAR</span>
                            <span class="text-[10px] text-gray-400 font-normal">ARRANCAR RELOJ</span>
                        </button>
                        <button onclick="triggerEvent('descanso')" class="btn-control h-16 bg-control-surface hover:bg-gray-700 rounded text-white font-bold flex flex-col items-center justify-center border-t-4 border-control-warning">
                            <span class="text-lg">⏸ PAUSA</span>
                            <span class="text-[10px] text-gray-400 font-normal">ENTRETIEMPO</span>
                        </button>
                        <button onclick="triggerEvent('fin')" class="btn-control h-16 bg-control-surface hover:bg-gray-700 rounded text-white font-bold flex flex-col items-center justify-center border-t-4 border-control-danger">
                            <span class="text-lg">⏹ FIN</span>
                            <span class="text-[10px] text-gray-400 font-normal">TERMINAR PARTIDO</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-8">
                    <div class="bg-control-panel p-4 rounded border border-white/5">
                        <h4 class="text-center font-bold text-white mb-4 border-b border-white/5 pb-2">{{ $match->team_home }}</h4>
                        <button onclick="triggerEvent('goal_home')" class="btn-control w-full py-4 bg-control-accent hover:bg-blue-500 text-white font-black text-xl rounded shadow-lg shadow-blue-900/20 mb-3">
                            ⚽ GOL
                        </button>
                        <div class="grid grid-cols-2 gap-2">
                            <button onclick="triggerEvent('yellow_card_home')" class="btn-control py-2 bg-yellow-600 hover:bg-yellow-500 text-white font-bold rounded text-xs">🟨 Amarilla</button>
                            <button onclick="triggerEvent('red_card_home')" class="btn-control py-2 bg-red-600 hover:bg-red-500 text-white font-bold rounded text-xs">🟥 Roja</button>
                        </div>
                        <button onclick="triggerEvent('substitution_home')" class="btn-control w-full mt-3 py-2 bg-control-surface hover:bg-gray-700 border border-white/10 rounded text-gray-300 text-xs font-bold">
                            🔄 Cambio Jugador
                        </button>
                    </div>

                    <div class="bg-control-panel p-4 rounded border border-white/5">
                        <h4 class="text-center font-bold text-white mb-4 border-b border-white/5 pb-2">{{ $match->team_away }}</h4>
                        <button onclick="triggerEvent('goal_away')" class="btn-control w-full py-4 bg-control-accent hover:bg-blue-500 text-white font-black text-xl rounded shadow-lg shadow-blue-900/20 mb-3">
                            ⚽ GOL
                        </button>
                        <div class="grid grid-cols-2 gap-2">
                            <button onclick="triggerEvent('yellow_card_away')" class="btn-control py-2 bg-yellow-600 hover:bg-yellow-500 text-white font-bold rounded text-xs">🟨 Amarilla</button>
                            <button onclick="triggerEvent('red_card_away')" class="btn-control py-2 bg-red-600 hover:bg-red-500 text-white font-bold rounded text-xs">🟥 Roja</button>
                        </div>
                        <button onclick="triggerEvent('substitution_away')" class="btn-control w-full mt-3 py-2 bg-control-surface hover:bg-gray-700 border border-white/10 rounded text-gray-300 text-xs font-bold">
                            🔄 Cambio Jugador
                        </button>
                    </div>
                </div>

                <div class="pt-8 mt-8 border-t border-white/5">
                    <button onclick="if(confirm('¿Seguro que deseas reiniciar todo a 0?')) triggerEvent('reset')" class="w-full py-3 bg-red-900/20 hover:bg-red-900/40 border border-red-900/50 text-red-500 rounded text-xs font-bold uppercase tracking-widest transition">
                        ⚠ Reiniciar Marcador y Cronómetro (Borrar Historial)
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="h-48 bg-black border-t border-white/10 flex flex-col shrink-0 font-mono text-xs">
        <div class="px-4 py-2 bg-control-panel border-b border-white/5 text-gray-500 font-bold flex justify-between">
            <span>TERMINAL DE SALIDA</span>
            <span class="text-control-success">● CONECTADO</span>
        </div>
        <div id="console-output" class="flex-1 overflow-y-auto p-4 space-y-1 text-gray-400">
            <div class="text-gray-600">Sistema listo. Esperando comandos...</div>
        </div>
    </div>

    <script type="module">
        const matchId = {{ $match->id }};
        const consoleOutput = document.getElementById('console-output');

        let gameMinute = {{ $currentMinute }};
        let isLive = @json($match->status == 'live');
        let status = "{{ $match->status }}";
        let timerInterval = null;

        // Inicializar UI
        updateStatusUI(status);
        if(isLive) startTimer();

        // Timer Local
        function startTimer() {
            if(timerInterval) clearInterval(timerInterval);
            timerInterval = setInterval(() => { if(gameMinute < 130) { gameMinute++; updateTimerUI(); } }, 60000);
            updateTimerUI();
        }

        function updateTimerUI() {
            document.getElementById('timer-display').innerText = gameMinute + "'";
        }

        function updateStatusUI(st) {
            const el = document.getElementById('status-indicator-text');
            const parent = document.getElementById('status-display');

            if(st === 'live') {
                el.innerText = "EN EL AIRE (ON AIR)";
                el.className = "text-sm font-black text-red-500 animate-pulse";
                parent.className = "bg-red-900/20 border border-red-500/50 rounded p-3 text-center";
            } else if (st === 'scheduled') {
                el.innerText = "EN ESPERA (STANDBY)";
                el.className = "text-sm font-black text-gray-400";
                parent.className = "bg-black/50 border border-white/10 rounded p-3 text-center";
            } else {
                el.innerText = st.toUpperCase();
                el.className = "text-sm font-black text-yellow-500";
                parent.className = "bg-yellow-900/20 border border-yellow-500/50 rounded p-3 text-center";
            }
        }

        // WebSockets Listener
        Echo.channel('partido.' + matchId)
            .listen('.evento.nuevo', (e) => {
                if (e.matchGame && e.matchGame.id !== matchId) return;

                logToConsole(`[RECIBIDO] ${e.type.toUpperCase()}: ${e.message}`, 'text-control-accent');

                // Sync Reloj
                if (e.minute > gameMinute) { gameMinute = e.minute; updateTimerUI(); }

                // Marcador
                if(e.type === 'goal_home') updateScore('score-home-display');
                if(e.type === 'goal_away') updateScore('score-away-display');

                // Estados
                if(e.type === 'inicio') { isLive=true; gameMinute=e.minute; startTimer(); updateStatusUI('live'); }
                if(e.type === 'fin') { clearInterval(timerInterval); updateStatusUI('finished'); }
                if(e.type === 'descanso') { clearInterval(timerInterval); updateStatusUI('break'); }

                // Reset
                if(e.type === 'reset') {
                    document.getElementById('score-home-display').innerText = "0";
                    document.getElementById('score-away-display').innerText = "0";
                    gameMinute = 0;
                    clearInterval(timerInterval);
                    document.getElementById('timer-display').innerText = "0'";
                    updateStatusUI('scheduled');
                    logToConsole("[SISTEMA] Reinicio completo ejecutado.", "text-red-500");
                }
            });

        // Trigger Eventos
        window.triggerEvent = function(type) {
            logToConsole(`> Enviando comando: ${type}...`, 'text-gray-300');

            // Feedback visual en el botón presionado (opcional si se desea agregar lógica compleja)

            fetch(`/arbitro/${matchId}/${type}`)
                .then(res => res.json())
                .then(data => {
                    logToConsole(`✔ Comando exitoso: ${data.message}`, 'text-green-500');
                })
                .catch(err => logToConsole(`❌ Error: ${err}`, 'text-red-500'));
        };

        function updateScore(id) {
            const el = document.getElementById(id);
            if(el) {
                el.innerText = parseInt(el.innerText) + 1;
                el.classList.add('text-white');
                setTimeout(() => el.classList.remove('text-white'), 200);
            }
        }

        function logToConsole(msg, cls) {
            const div = document.createElement('div');
            div.className = `font-mono ${cls}`;
            div.innerText = `[${new Date().toLocaleTimeString()}] ${msg}`;
            consoleOutput.prepend(div);
        }
    </script>
</body>
</html>
