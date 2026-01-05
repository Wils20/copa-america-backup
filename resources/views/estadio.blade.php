<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $match->team_home }} vs {{ $match->team_away }} | Transmisión</title>
    <script src="https://cdn.tailwindcss.com"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        stream: { bg: '#0f1014', card: '#18191f', accent: '#e50914', hover: '#24252e', chat: '#121317' }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f1014; overflow-x: hidden; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #121317; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        .animate-slide-in { animation: slideIn 0.3s ease-out forwards; }
        @keyframes slideIn { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
        .animate-blink { animation: blink 1.5s infinite; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }
    </style>
</head>
<body class="text-gray-200 h-screen flex flex-col">

    <div id="toast-container" class="fixed top-24 left-1/2 transform -translate-x-1/2 z-[60] flex flex-col gap-2 w-auto pointer-events-none"></div>

    <nav class="h-16 px-6 flex justify-between items-center bg-[#0f1014] border-b border-white/5 shrink-0 z-50">
        <div class="flex items-center gap-4">
            <a href="/" class="text-gray-400 hover:text-white transition flex items-center gap-2 text-sm font-bold uppercase tracking-wider">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver
            </a>
            <div class="h-6 w-px bg-white/10"></div>
            <h1 class="font-bold text-lg hidden md:block">{{ $match->league_name }}</h1>
        </div>
        <div id="status-badge-nav" class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest flex items-center gap-2 {{ $match->status == 'live' ? 'bg-red-600/20 text-red-500 border border-red-600/30' : 'bg-gray-800 text-gray-400' }}">
            @if($match->status == 'live') <span class="w-2 h-2 bg-red-500 rounded-full animate-blink"></span> EN VIVO
            @else <span>{{ strtoupper($match->status == 'finished' ? 'Finalizado' : 'Programado') }}</span> @endif
        </div>
    </nav>

    <div class="flex-1 overflow-hidden flex flex-col lg:flex-row">

        <div class="flex-1 flex flex-col bg-black relative overflow-y-auto">

            <div class="relative aspect-video w-full bg-black flex items-center justify-center overflow-hidden group">
                <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1518091043644-c1d4457512c6?q=80&w=2542')] bg-cover bg-center opacity-30"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-black/60"></div>

                <div class="absolute top-6 left-6 z-20 flex flex-col gap-1">
                    <div class="bg-black/80 backdrop-blur-md rounded border border-white/10 overflow-hidden shadow-2xl flex items-center">
                        <div class="px-4 py-2 flex items-center gap-3 border-r border-white/10">
                            <span class="font-black text-white text-xl">{{ substr($match->team_home, 0, 3) }}</span>
                            <span id="score-home" class="font-mono font-bold text-2xl text-stream-accent bg-white/10 px-2 rounded">{{ $match->score_home }}</span>
                        </div>
                        <div class="px-4 py-2 flex items-center gap-3">
                            <span id="score-away" class="font-mono font-bold text-2xl text-stream-accent bg-white/10 px-2 rounded">{{ $match->score_away }}</span>
                            <span class="font-black text-white text-xl">{{ substr($match->team_away, 0, 3) }}</span>
                        </div>
                        <div class="bg-stream-accent text-white px-3 py-3 font-mono font-bold text-sm min-w-[60px] text-center" id="game-timer">{{ $currentMinute }}'</div>
                    </div>
                </div>

                <div class="relative z-10 transition-transform duration-300 transform group-hover:scale-110 cursor-pointer">
                    <div class="w-20 h-20 bg-stream-accent/90 hover:bg-stream-accent rounded-full flex items-center justify-center shadow-[0_0_30px_rgba(229,9,20,0.5)] backdrop-blur-sm">
                        <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-[#0b0c10] border-t border-white/5">
                <h2 class="text-2xl md:text-3xl font-bold text-white mb-2">{{ $match->team_home }} vs {{ $match->team_away }}</h2>
                <div class="flex gap-4 text-sm text-gray-400">
                    <span>{{ $match->stadium }}</span> • <span>{{ $match->referee }}</span>
                </div>
            </div>

            <div class="p-6 bg-[#0b0c10] border-t border-white/5 space-y-6 pb-20">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    BIG DATA & ANALYTICS
                </h3>

                <div>
                    <div class="flex justify-between text-[10px] uppercase font-bold text-gray-400 mb-2">
                        <span>{{ $match->team_home }}</span> <span>Empate</span> <span>{{ $match->team_away }}</span>
                    </div>
                    <div class="h-2 bg-gray-800 rounded-full flex overflow-hidden">
                        <div id="prob-home" class="bg-blue-500 h-full transition-all duration-1000" style="width: {{ $match->stats['win_prob_home'] ?? 33 }}%"></div>
                        <div id="prob-draw" class="bg-gray-600 h-full transition-all duration-1000" style="width: {{ $match->stats['win_prob_draw'] ?? 34 }}%"></div>
                        <div id="prob-away" class="bg-red-500 h-full transition-all duration-1000" style="width: {{ $match->stats['win_prob_away'] ?? 33 }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs font-mono mt-1 text-gray-300">
                        <span id="txt-prob-home">{{ round($match->stats['win_prob_home'] ?? 33) }}%</span>
                        <span id="txt-prob-draw">{{ round($match->stats['win_prob_draw'] ?? 34) }}%</span>
                        <span id="txt-prob-away">{{ round($match->stats['win_prob_away'] ?? 33) }}%</span>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-8 text-center border-t border-white/5 pt-6">
                    <div>
                        <div class="text-[10px] text-gray-500 uppercase font-bold mb-1">Posesión</div>
                        <div class="flex items-end justify-center gap-2 font-mono text-lg font-bold">
                            <span id="stat-pos-home" class="text-blue-400">{{ $match->stats['possession_home'] ?? 50 }}%</span>
                            <span class="text-xs text-gray-600 mb-1">vs</span>
                            <span id="stat-pos-away" class="text-red-400">{{ $match->stats['possession_away'] ?? 50 }}%</span>
                        </div>
                        <div class="h-1 w-full bg-gray-800 mt-1 rounded overflow-hidden flex">
                            <div id="bar-pos-home" class="bg-blue-500/50 h-full transition-all duration-500" style="width: {{ $match->stats['possession_home'] ?? 50 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] text-gray-500 uppercase font-bold mb-1">Tiros Total</div>
                        <div class="flex items-end justify-center gap-2 font-mono text-lg font-bold">
                            <span id="stat-shots-home" class="text-white">{{ $match->stats['shots_home'] ?? 0 }}</span>
                            <span class="text-xs text-gray-600 mb-1">-</span>
                            <span id="stat-shots-away" class="text-white">{{ $match->stats['shots_away'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] text-gray-500 uppercase font-bold mb-1">Córners</div>
                        <div class="flex items-end justify-center gap-2 font-mono text-lg font-bold">
                            <span id="stat-corners-home" class="text-white">{{ $match->stats['corners_home'] ?? 0 }}</span>
                            <span class="text-xs text-gray-600 mb-1">-</span>
                            <span id="stat-corners-away" class="text-white">{{ $match->stats['corners_away'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:w-[400px] w-full bg-stream-card border-l border-white/5 flex flex-col h-[50vh] lg:h-auto">
            <div class="p-4 border-b border-white/5 bg-[#1a1c24] flex justify-between items-center">
                <h3 class="font-bold text-white flex items-center gap-2"><span class="w-2 h-2 bg-stream-accent rounded-full animate-pulse"></span> MINUTO A MINUTO</h3>
                <span class="text-xs text-gray-500 bg-black/20 px-2 py-1 rounded">Auto-scroll ON</span>
            </div>

            <div id="event-feed-container" class="flex-1 overflow-y-auto p-4 space-y-3 bg-stream-chat">

                @foreach($history as $event)
                    @php
                        $b='border-gray-700'; $bg='bg-gray-800'; $tx='text-gray-300'; $ic='⏱';
                        // Detectamos el tipo de evento con PHP
                        if(Str::contains($event->type, 'goal')) { $b='border-green-500/50'; $bg='bg-green-500/10 text-green-500'; $tx='text-white font-bold'; $ic='⚽'; }
                        elseif(Str::contains($event->type, 'red')) { $b='border-red-500/50'; $bg='bg-red-500/10 text-red-500'; $ic='🟥'; }
                        elseif(Str::contains($event->type, 'yellow')) { $b='border-yellow-500/50'; $bg='bg-yellow-500/10 text-yellow-500'; $ic='🟨'; }
                        elseif(Str::contains($event->type, 'substitution')) { $b='border-blue-500/50'; $bg='bg-blue-500/10 text-blue-400'; $ic='🔄'; }
                    @endphp

                    <div class="flex gap-3 text-sm p-3 rounded bg-[#1e2029] border-l-2 {{ $b }} shadow-sm">
                        <div class="font-mono font-bold text-gray-400 min-w-[30px]">{{ $event->minute }}'</div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-5 h-5 flex items-center justify-center rounded {{ $bg }} text-xs">{{ $ic }}</span>
                                <span class="{{ $tx }}">{{ $event->message }}</span>
                            </div>
                            @if($event->player_name)
                                <div class="text-xs text-gray-500 pl-7">{{ $event->player_name }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    <script type="module">
        let gameMinute = {{ $currentMinute }};
        let isLive = @json($match->status == 'live');
        let timerInterval = null;
        const currentMatchId = {{ $match->id }};

        if(isLive) startTimer();

        function startTimer() {
            if(timerInterval) clearInterval(timerInterval);
            timerInterval = setInterval(() => { if(gameMinute < 125) { gameMinute++; updateTimerUI(); } }, 60000);
            updateTimerUI();
        }
        function updateTimerUI() { const el = document.getElementById('game-timer'); if(el) el.innerText = `${gameMinute}'`; }

        Echo.channel('partido.' + currentMatchId).listen('.evento.nuevo', (e) => {
            if (e.matchGame && e.matchGame.id !== currentMatchId) return;

            // Actualizar Estadísticas Big Data
            if (e.matchGame && e.matchGame.stats) updateStatsUI(e.matchGame.stats);

            // Si es solo update de stats, no hacemos más nada visual
            if(e.type === 'stats_update') return;

            // Lógica Juego
            if (e.minute > gameMinute) { gameMinute = e.minute; updateTimerUI(); }
            if(e.type === 'inicio') { isLive = true; gameMinute = e.minute; startTimer(); document.getElementById('status-badge-nav').innerHTML = '<span class="w-2 h-2 bg-red-500 rounded-full animate-blink"></span> EN VIVO'; }
            if(e.type === 'fin' || e.type === 'descanso' || e.type === 'reset') { clearInterval(timerInterval); if(e.type === 'reset') location.reload(); }
            if(e.type.includes('goal_home')) updateScore('score-home');
            if(e.type.includes('goal_away')) updateScore('score-away');

            addEventToFeed(e);
            showToast(e);
        });

        function updateScore(id) {
            const el = document.getElementById(id);
            if(el) { el.innerText = parseInt(el.innerText) + 1; el.classList.add('bg-stream-accent', 'text-white'); setTimeout(() => el.classList.remove('bg-stream-accent', 'text-white'), 500); }
        }

        function updateStatsUI(stats) {
            // Probabilidad
            document.getElementById('prob-home').style.width = stats.win_prob_home + '%';
            document.getElementById('prob-draw').style.width = stats.win_prob_draw + '%';
            document.getElementById('prob-away').style.width = stats.win_prob_away + '%';
            document.getElementById('txt-prob-home').innerText = Math.round(stats.win_prob_home) + '%';
            document.getElementById('txt-prob-draw').innerText = Math.round(stats.win_prob_draw) + '%';
            document.getElementById('txt-prob-away').innerText = Math.round(stats.win_prob_away) + '%';
            // Posesión
            document.getElementById('stat-pos-home').innerText = stats.possession_home + '%';
            document.getElementById('stat-pos-away').innerText = stats.possession_away + '%';
            document.getElementById('bar-pos-home').style.width = stats.possession_home + '%';
            // Contadores
            document.getElementById('stat-shots-home').innerText = stats.shots_home;
            document.getElementById('stat-shots-away').innerText = stats.shots_away;
            document.getElementById('stat-corners-home').innerText = stats.corners_home;
            document.getElementById('stat-corners-away').innerText = stats.corners_away;
        }

        function addEventToFeed(e) {
            const feed = document.getElementById('event-feed-container');
            let b='border-gray-700', bg='bg-gray-800', tx='text-gray-300', ic='⏱';
            if(e.type.includes('goal')) { b='border-green-500/50'; bg='bg-green-500/10 text-green-500'; tx='text-white font-bold'; ic='⚽'; }
            else if(e.type.includes('red')) { b='border-red-500/50'; bg='bg-red-500/10 text-red-500'; ic='🟥'; }
            else if(e.type.includes('yellow')) { b='border-yellow-500/50'; bg='bg-yellow-500/10 text-yellow-500'; ic='🟨'; }
            else if(e.type.includes('substitution')) { b='border-blue-500/50'; bg='bg-blue-500/10 text-blue-400'; ic='🔄'; }

            const div = document.createElement('div');
            div.className = `flex gap-3 text-sm p-3 rounded bg-[#1e2029] border-l-2 ${b} shadow-sm animate-slide-in`;
            div.innerHTML = `<div class="font-mono font-bold text-gray-400 min-w-[30px]">${e.minute}'</div><div class="flex-1"><div class="flex items-center gap-2 mb-1"><span class="w-5 h-5 flex items-center justify-center rounded ${bg} text-xs">${ic}</span> <span class="${tx}">${e.message}</span></div>${e.playerName ? `<div class="text-xs text-gray-500 pl-7">${e.playerName}</div>` : ''}</div>`;
            feed.prepend(div);
        }

        function showToast(e) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            let bg = 'bg-gray-800', icon = '📢';
            if(e.type.includes('goal')) { bg = 'bg-stream-accent'; icon = 'GOOOL'; }
            toast.className = `px-6 py-3 rounded-full shadow-2xl ${bg} text-white font-bold flex items-center gap-3 transform transition-all duration-300 translate-y-4 opacity-0`;
            toast.innerHTML = `<span>${icon}</span> <span>${e.message}</span>`;
            container.appendChild(toast);
            requestAnimationFrame(() => toast.classList.remove('translate-y-4', 'opacity-0'));
            setTimeout(() => { toast.classList.add('-translate-y-4', 'opacity-0'); setTimeout(() => toast.remove(), 300); }, 3000);
        }
    </script>
</body>
</html>
