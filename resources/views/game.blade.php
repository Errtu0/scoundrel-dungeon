<div class="room-grid">
    @foreach($game->current_room as $index => $card)
        <form action="/play-card/{{ $index }}" method="POST">
            @csrf
            <button class="card-sprite">
                {{ $card['suit'] }} {{ $card['value'] }}
            </button>
        </form>
    @endforeach
</div>
