<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoundrel - The Deep Deck</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/RPG-Awesome/0.1.9/css/rpg-awesome.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=MedievalSharp&display=swap');
        [x-cloak] { display: none !important; }

        body {
            font-family: 'MedievalSharp', cursive;
            background-color: #000;
            overflow: hidden;
        }

        .dungeon-bg {
            background: linear-gradient(to bottom, rgba(0,0,0,0.5), rgba(0,0,0,0.9)),
                        url('https://images.unsplash.com/photo-1728755833852-2c138c84cfb1?q=80&w=2072&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            transition: transform 0.1s ease-out, filter 0.5s ease; /* Added filter transition */
        }

        /* 2. Rulebook Background Blur */
        .blur-bg { filter: blur(12px) brightness(0.3); }

        /* GHOST CARD EFFECT */
        .ghost-card {
            opacity: 0;
            transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
            transform: translate(-50%, 0%) rotate(-15deg);
            pointer-events: none;
        }
        .btn-container:hover .ghost-card {
            opacity: 0.4;
            transform: translate(-50%, -130%) rotate(0deg);
            filter: drop-shadow(0 0 15px rgba(255, 50, 0, 0.8));
        }

        /* 1. Subtle Rising Ash (Embers) */
        .ember {
            position: absolute;
            background: rgba(255, 100, 0, 0.5); /* Subtle orange/red */
            border-radius: 50%;
            pointer-events: none;
            animation: rise 5s infinite linear;
            opacity: 0;
        }
        @keyframes rise {
            0% { transform: translateY(110vh) translateX(0); opacity: 0; }
            40% { opacity: 0.7; }
            80% { opacity: 0.7; }
            100% { transform: translateY(-10vh) translateX(30px); opacity: 0; }
        }


        .book-scroll::-webkit-scrollbar { width: 6px; }
        .book-scroll::-webkit-scrollbar-track { background: #2a1b15; }
        .book-scroll::-webkit-scrollbar-thumb { background: #8b4513; }
    </style>
</head>
<body x-data="{
    openBook: false,
    posX: 0,
    posY: 0,
    updateMouse(e) {
        this.posX = (e.clientX / window.innerWidth - 0.5) * 20;
        this.posY = (e.clientY / window.innerHeight - 0.5) * 20;
    }
}" @mousemove="updateMouse($event)">

    <div class="fixed inset-0 dungeon-bg z-0"
         :class="openBook ? 'blur-bg' : ''"
         :style="`transform: translate(${posX}px, ${posY}px) scale(1.1);` ">
    </div>

    @foreach(range(1, 25) as $i)
        <div class="ember z-10" style=" left: {{ rand(0, 100) }}%; width: {{ rand(1, 3) }}px; height: {{ rand(1, 3) }}px; animation-delay: {{ $i * 0.2 }}s; animation-duration: {{ rand(4, 7) }}s;"></div>
    @endforeach

    <div class="relative z-20 min-h-screen flex flex-col items-center justify-center text-center space-y-12">

        <h1 class="text-6xl md:text-7xl font-bold tracking-[0.25em] text-red-700 drop-shadow-[0_10px_20px_rgba(0,0,0,1)]">
            SCOUNDREL
        </h1>

        <div class="flex flex-col items-center gap-10">
            <div class="relative btn-container">
                <div class="absolute left-1/2 ghost-card w-24 h-36 bg-white/10 border-2 border-white/20 rounded shadow-2xl flex items-center justify-center text-red-600 text-5xl">
                    ♠
                </div>

                <form action="{{ route('game.start') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-12 py-5 bg-red-950/90 border-2 border-red-800 hover:bg-red-700 transition-all duration-500 rounded-sm font-bold text-2xl tracking-[0.2em] uppercase shadow-[0_0_30px_rgba(0,0,0,0.5)] hover:shadow-[0_0_50px_rgba(153,27,27,0.4)] text-white">
                        Enter the Void
                    </button>
                </form>
            </div>

            <button @click="openBook = true" class="flex flex-col items-center gap-3 text-yellow-700 hover:text-yellow-500 transition-all group cursor-pointer">
                <i class="ra ra-scroll ra-2x group-hover:scale-125 transition-transform duration-300"></i>
                <span class="text-xs uppercase tracking-[0.5em] opacity-60 group-hover:opacity-100">The Ancient Rules</span>
            </button>
        </div>
    </div>

    <div x-show="openBook"
         x-cloak
         x-transition:enter="transition ease-out duration-400"
         x-transition:enter-start="opacity-0 translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">

        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="openBook = false"></div>

        <div class="relative bg-[#f5e6c8] text-slate-900 max-w-2xl w-full max-h-[85vh] overflow-y-auto p-8 md:p-12 rounded shadow-[20px_20px_60px_rgba(0,0,0,0.8)] border-[12px] border-[#3b2a1a] book-scroll">

            <button @click="openBook = false" class="absolute top-4 right-6 text-3xl font-bold hover:text-red-800 transition-colors">✕</button>

            <div class="text-center mb-12">
                <h2 class="text-4xl font-black uppercase tracking-tighter border-b-4 border-slate-900 inline-block px-4 pb-1">The Explorer's Journal</h2>
            </div>

            <div class="space-y-12">
                <div class="flex flex-col md:flex-row gap-6 items-center">
                    <div class="flex-shrink-0 w-24 h-32 bg-white border-2 border-slate-400 rounded-md flex flex-col items-center justify-center shadow-lg transform -rotate-3">
                        <span class="text-xl font-bold">10</span>
                        <span class="text-5xl">♣</span>
                    </div>
                    <div class="text-center md:text-left">
                        <h3 class="text-2xl font-bold text-red-900 uppercase">Monsters (Clubs & Spades)</h3>
                        <p class="text-lg leading-snug font-serif italic">"They wait in the dark. Their value is the blood they will spill from your veins."</p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-6 items-center">
                    <div class="flex-shrink-0 w-24 h-32 bg-white border-2 border-slate-400 rounded-md flex flex-col items-center justify-center shadow-lg transform rotate-2">
                        <span class="text-xl font-bold">8</span>
                        <span class="text-5xl text-blue-800">♦</span>
                    </div>
                    <div class="text-center md:text-left">
                        <h3 class="text-2xl font-bold text-blue-900 uppercase">Arsenal (Diamonds)</h3>
                        <p class="text-lg leading-snug font-serif italic">"A sharp blade protects the heart, but it grows dull. Slay only those weaker than your last kill."</p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-6 items-center">
                    <div class="flex-shrink-0 w-24 h-32 bg-white border-2 border-slate-400 rounded-md flex flex-col items-center justify-center shadow-lg transform -rotate-2">
                        <span class="text-xl font-bold">J</span>
                        <span class="text-5xl text-red-600">♥</span>
                    </div>
                    <div class="text-center md:text-left">
                        <h3 class="text-2xl font-bold text-green-900 uppercase">Elixirs (Hearts)</h3>
                        <p class="text-lg leading-snug font-serif italic">"Liquid life, but a bitter pill. One draught per room, or the toxicity will waste the rest."</p>
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-slate-400 text-center">
                    <p class="text-xl font-bold italic tracking-wide">"Flee if you must, but the deck always returns."</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
