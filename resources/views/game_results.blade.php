<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>The Journey's End</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/RPG-Awesome/0.1.9/css/rpg-awesome.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=MedievalSharp&display=swap');
        body { font-family: 'MedievalSharp', cursive; background: #000; color: #e2e8f0; overflow: hidden; }

        .result-bg {
            position: fixed; inset: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.8), rgba(0,0,0,0.95)),
                        url('https://images.unsplash.com/photo-1728755833852-2c138c84cfb1?q=80&w=2072&auto=format&fit=crop');
            background-size: cover; filter: grayscale(1) brightness(0.4);
        }

        .parchment {
            background: #1a1512;
            border: 2px solid {{ $status === 'victory' ? '#854d0e' : '#7f1d1d' }};
            box-shadow: 0 0 50px {{ $status === 'victory' ? 'rgba(133, 77, 14, 0.3)' : 'rgba(127, 29, 29, 0.5)' }};
        }

        @keyframes fadeIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
        .animate-result { animation: fadeIn 0.8s ease-out forwards; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">
    <div class="result-bg"></div>

    <div class="relative z-10 max-w-md w-full parchment p-8 rounded-lg text-center animate-result">
        @if($status === 'victory')
            <i class="ra ra-laurels ra-5x text-yellow-600 mb-4 inline-block"></i>
            <h1 class="text-4xl font-bold text-yellow-500 mb-2 uppercase tracking-tighter">Survivor</h1>
            <p class="text-gray-400 mb-6 italic text-sm">You emerged from the abyss, heavy with gold and scars.</p>
        @else
            <i class="ra ra-skull-double ra-5x text-red-700 mb-4 inline-block"></i>
            <h1 class="text-4xl font-bold text-red-600 mb-2 uppercase tracking-tighter">Consumed</h1>
            <p class="text-gray-400 mb-6 italic text-sm">The darkness claims another soul. Your bones will pave the path for the next.</p>
        @endif

        <div class="grid grid-cols-2 gap-4 mb-8 text-left border-y border-white/5 py-4">
            <div>
                <span class="text-[10px] uppercase text-gray-500 block">Final Health</span>
                <span class="text-xl {{ $status === 'victory' ? 'text-green-500' : 'text-red-500' }}">
                    {{ max(0, $game->health) }} / 20
                </span>
            </div>
            <div>
                <span class="text-[10px] uppercase text-gray-500 block">Final Weapon</span>
                <span class="text-xl text-blue-400">{{ $game->weapon_val ?? 'None' }}</span>
            </div>
        </div>

        <div class="space-y-4">
            <a href="{{ route('game.start') }}"
               class="block w-full py-4 bg-{{ $status === 'victory' ? 'yellow' : 'red' }}-900/40 border border-{{ $status === 'victory' ? 'yellow' : 'red' }}-800 text-{{ $status === 'victory' ? 'yellow' : 'red' }}-100 hover:bg-{{ $status === 'victory' ? 'yellow' : 'red' }}-800 transition-all uppercase tracking-[0.2em] font-bold">
                Enter Once More
            </a>

            <p class="text-[10px] text-gray-600 uppercase tracking-widest">Scoundrel v1.0 - The Void Beckons</p>
        </div>
    </div>
</body>
</html>
