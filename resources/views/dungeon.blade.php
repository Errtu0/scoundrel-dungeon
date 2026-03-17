<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scoundrel - The Dungeon Depth</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/RPG-Awesome/0.1.9/css/rpg-awesome.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=MedievalSharp&display=swap');
        [x-cloak] { display: none !important; }

        body {
            font-family: 'MedievalSharp', cursive;
            background-color: #000;
            color: #e2e8f0;
            overflow-x: hidden;
        }

        .dungeon-bg {
            background: linear-gradient(to bottom, rgba(0,0,0,0.7), rgba(0,0,0,0.9)),
                        url('https://images.unsplash.com/photo-1728755833852-2c138c84cfb1?q=80&w=2072&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            filter: blur(4px) brightness(0.6);
        }

        .health-vignette {
            position: fixed; inset: 0; pointer-events: none;
            box-shadow: inset 0 0 100px rgba(185, 28, 28, 0.6);
            z-index: 50; animation: blood-pulse 2s infinite;
        }
        @keyframes blood-pulse { 0%, 100% { opacity: 0.3; } 50% { opacity: 0.7; } }

        .card-inner { transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
        .card-inner:hover { transform: translateY(-15px) scale(1.02); }

        .log-scroll::-webkit-scrollbar { width: 4px; }
        .log-scroll::-webkit-scrollbar-thumb { background: #450a0a; border-radius: 10px; }

        .ember {
            position: absolute; background: rgba(255, 100, 0, 0.3);
            border-radius: 50%; pointer-events: none;
            animation: rise 6s infinite linear; opacity: 0;
        }
        @keyframes rise {
            0% { transform: translateY(110vh) translateX(0); opacity: 0; }
            20% { opacity: 0.5; } 100% { transform: translateY(-10vh) translateX(30px); opacity: 0; }
        }

        html, body {
            height: 100%;
            overflow: hidden; /* This kills the repeating scrollbar on the right */
        }

        /* Ensure the main container can still handle layout but won't trigger scroll */
        .relative.z-20 {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
    </style>
</head>
<body x-data="{
    loaded: false,
    shouldAnimate: {{ session('animate') ? 'true' : 'false' }},
    posX: 0,
    posY: 0,
    updateMouse(e) {
        this.posX = (e.clientX / window.innerWidth - 0.5) * 15;
        this.posY = (e.clientY / window.innerHeight - 0.5) * 15;
    }
}" @mousemove="updateMouse($event)" x-init="setTimeout(() => loaded = true, 50)">

    @if($game->health <= 7)
        <div class="health-vignette"></div>
    @endif

    <div class="fixed inset-0 dungeon-bg z-0" :style="`transform: translate(${posX}px, ${posY}px) scale(1.05);` "></div>

    @foreach(range(1, 20) as $i)
        <div class="ember z-10" style="left: {{ rand(0, 100) }}%; width: {{ rand(1, 3) }}px; height: {{ rand(1, 3) }}px; animation-delay: {{ $i * 0.3 }}s;"></div>
    @endforeach

    <div class="relative z-20 max-w-6xl mx-auto p-4 md:p-8">

        <div class="flex justify-between items-end mb-12 bg-black/40 backdrop-blur-md p-6 rounded-t-lg border-b border-red-900/50">
            <div class="text-left">
                <h2 class="text-red-700 uppercase tracking-widest text-xs font-bold mb-1">Vitality</h2>
                <div class="flex items-baseline gap-1">
                    <p class="text-5xl font-bold {{ $game->health <= 5 ? 'animate-pulse text-red-500' : 'text-green-600' }}">
                        {{ $game->health }}
                    </p>
                    <span class="text-xl text-gray-500">/ 20</span>
                </div>
            </div>

            <div class="text-right">
                <h2 class="text-blue-700 uppercase tracking-widest text-xs font-bold mb-1">Arsenal</h2>
                <p class="text-5xl font-bold text-blue-500">{{ $game->weapon_val ?? 'None' }}</p>
                @if($game->last_slain_val)
                    <p class="text-[10px] text-blue-400/60 uppercase mt-1 italic">Slay < {{ $game->last_slain_val }}</p>
                @endif
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-8 items-start justify-center mb-16">

            <div class="flex flex-col items-center group">
                <div class="relative w-24 h-36 mb-4">
                    <div class="absolute inset-0 bg-red-950 border-2 border-red-900 rounded-md shadow-2xl translate-x-2 translate-y-2"></div>
                    <div class="absolute inset-0 bg-red-900 border-2 border-red-800 rounded-md shadow-xl translate-x-1 translate-y-1"></div>

                    <div class="absolute inset-0 bg-cover bg-center border-2 border-red-700 rounded-md shadow-lg flex items-center justify-center overflow-hidden"
                         style="background-image: url('{{ asset('images/cardback.png') }}');">
                        <div class="absolute inset-0 bg-black/20"></div>
                        <i class="ra ra-pawn ra-2x text-red-900/40 relative z-10"></i>
                    </div>
                </div>
                <span class="text-xs uppercase tracking-[0.3em] text-gray-600 font-bold">The Abyss</span>
                <span class="text-lg text-gray-400">{{ count($game->deck) }}</span>
            </div>

            <div class="flex-1 grid grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($game->current_room as $index => $card)
                    @php
                        $isRed = in_array($card['suit'], ['hearts', 'diamonds']);
                        $val = $card['value'];
                        if($val == 11) $val = 'J'; elseif($val == 12) $val = 'Q';
                        elseif($val == 13) $val = 'K'; elseif($val == 14) $val = 'A';
                    @endphp

                    <div x-show="loaded"
                         @if(session('animate'))
                         x-transition:enter="transition ease-out duration-700"
                         x-transition:enter-start="opacity-0 -translate-x-40 rotate-[-20deg]"
                         x-transition:enter-end="opacity-100 translate-x-0 rotate-0"
                         style="transition-delay: {{ $index * 150 }}ms"
                         @endif
                         class="card-inner">

                        <form action="{{ route('game.play', [$game->id, $index]) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full aspect-[2/3] bg-[#fdfaf5] rounded-md shadow-[0_10px_30px_rgba(0,0,0,0.5)] flex flex-col items-center justify-between p-4 border-2 border-gray-300 group relative overflow-hidden">
                                <div class="absolute inset-0 opacity-[0.03] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/paper-fibers.png')]"></div>
                                <div class="w-full text-left font-bold text-2xl {{ $isRed ? 'text-red-600' : 'text-slate-900' }}">{{ $val }}</div>
                                <div class="text-6xl {{ $isRed ? 'text-red-600' : 'text-slate-900' }} drop-shadow-sm">
                                    @if($card['suit'] == 'hearts') ♥ @elseif($card['suit'] == 'diamonds') ♦ @elseif($card['suit'] == 'spades') ♠ @else ♣ @endif
                                </div>
                                <div class="w-full text-right font-bold text-2xl rotate-180 {{ $isRed ? 'text-red-600' : 'text-slate-900' }}">{{ $val }}</div>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex flex-col items-center gap-6 mb-16">
            <div class="flex justify-center gap-6">
                @if($game->can_flee)
                    <form action="{{ route('game.flee', $game->id) }}" method="POST">
                        @csrf
                        <button class="px-8 py-3 border border-yellow-800 text-yellow-700 hover:text-yellow-400 hover:border-yellow-400 transition-all uppercase tracking-widest text-sm bg-black/20">Flee Room</button>
                    </form>
                @else
                    <button disabled class="px-8 py-3 border border-gray-800 text-gray-600 cursor-not-allowed uppercase tracking-widest text-sm opacity-40">Path Blocked</button>
                @endif

                @if(count($game->current_room) <= 1 && count($game->deck) > 0)
                    <form action="{{ route('game.nextRoom', $game->id) }}" method="POST">
                        @csrf
                        <button class="px-10 py-3 bg-red-950/40 border border-red-800 text-red-700 hover:bg-red-800 hover:text-red-100 transition-all uppercase tracking-widest text-sm animate-pulse">Move Deeper</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="max-w-2xl mx-auto bg-[#131110] border-l-4 border-red-900 p-6 rounded-r-lg shadow-2xl relative overflow-hidden">
            <i class="ra ra-scroll absolute -right-6 -bottom-6 text-white/5 text-9xl rotate-12"></i>
            <div class="flex items-center gap-3 mb-4 border-b border-white/5 pb-2">
                <i class="ra ra-quill-ink text-red-800 text-xl"></i>
                <span class="text-xs uppercase tracking-[0.3em] text-gray-500 font-bold">Chronicle of the Void</span>
            </div>

            <div class="h-32 overflow-y-auto font-mono text-xs space-y-3 log-scroll relative z-10 pr-2">
                @if(session('combat_history'))
                    @foreach(session('combat_history') as $log)
                        @php
                            $colorClass = 'text-gray-400';
                            if(Str::contains($log, ['barehanded', 'Took'])) $colorClass = 'text-red-500/90';
                            if(Str::contains($log, ['restored', 'potion'])) $colorClass = 'text-green-500/90';
                            if(Str::contains($log, ['weapon'])) $colorClass = 'text-blue-400/90';
                            if(Str::contains($log, ['fled', 'move'])) $colorClass = 'text-yellow-600/90';
                        @endphp
                        <div class="flex items-start gap-3 border-b border-white/5 pb-2 last:border-0">
                            <span class="text-red-900 font-bold">»</span>
                            <p class="{{ $colorClass }} leading-relaxed">{{ $log }}</p>
                        </div>
                    @endforeach
                @else
                    <div class="flex items-start gap-2 opacity-30">
                        <span class="text-gray-700">»</span>
                        <p class="text-gray-500 italic">The stone walls watch in silence...</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
