<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use App\Services\CardService;
use Illuminate\Http\Request;

class GameController extends Controller
{
    protected $cardService;

    public function __construct(CardService $cardService)
    {
        $this->cardService = $cardService;
    }

    public function start()
    {
        $deck = $this->cardService->generateScoundrelDeck();
        $room = array_splice($deck, 0, 4);

        $game = GameSession::create([
            'health' => 20,
            'deck' => $deck,
            'current_room' => $room,
            'weapon_val' => null,
            'last_slain_val' => null,
            'can_flee' => true,
            'drank_potion' => false,
        ]);

        // Initialize empty history
        session(['combat_history' => ['You step into the cold, damp dungeon...']]);

        return redirect()->route('game.show', $game->id);
    }

    public function show($id)
    {
        $game = GameSession::findOrFail($id);
        return view('dungeon', compact('game'));
    }

    public function playCard(Request $request, $id, $cardIndex)
    {
        $game = GameSession::findOrFail($id);
        $room = $game->current_room;

        if (!isset($room[$cardIndex])) {
            return redirect()->back();
        }

        $card = $room[$cardIndex];
        $message = "";

        // 1. Process Suit Logic and generate dynamic messages
        if (in_array($card['suit'], ['spades', 'clubs'])) {
            $message = $this->handleMonster($game, $card);
        } elseif ($card['suit'] === 'hearts') {
            $message = $this->handlePotion($game, $card);
        } elseif ($card['suit'] === 'diamonds') {
            $message = $this->handleWeapon($game, $card);
        }

        // 2. Log History Management
        $history = session()->get('combat_history', []);
        array_unshift($history, $message); // Add latest move to top
        session(['combat_history' => array_slice($history, 0, 15)]); // Keep last 15 moves

        // 3. Remove the card
        unset($room[$cardIndex]);
        $game->current_room = array_values($room);

        // 4. Death Check
        if ($game->health <= 0) {
            $game->save();
            return view('game_results', ['status' => 'death', 'game' => $game]);
        }

        // 5. Automatic Transition
        if (count($game->current_room) === 0 && count($game->deck) > 0) {
            $this->fillRoom($game);
            $history = session()->get('combat_history', []);
            array_unshift($history, "The room is cleared. You move forward.");
            session(['combat_history' => $history]);
        }

        // 6. Victory Check
        if (count($game->current_room) === 0 && count($game->deck) === 0) {
            $game->save();
            return view('game_results', ['status' => 'victory', 'game' => $game]);
        }

        $game->save();
        return redirect()->route('game.show', $game->id);
    }

    public function nextRoom($id)
    {
        $game = GameSession::findOrFail($id);

        if (count($game->current_room) === 1 && count($game->deck) > 0) {
            $this->fillRoom($game);

            $history = session()->get('combat_history', []);
            array_unshift($history, "You left a card behind and moved deeper into the dark.");
            session(['combat_history' => $history]);

            $game->save();
            return redirect()->route('game.show', $game->id);
        }

        return redirect()->back()->with('error', 'You must play at least 3 cards to move on.');
    }

    public function flee($id)
    {
        $game = GameSession::findOrFail($id);

        if (!$game->can_flee || count($game->deck) < 4) {
            return redirect()->back();
        }

        $currentDeck = $game->deck;
        foreach ($game->current_room as $card) {
            $currentDeck[] = $card;
        }

        shuffle($currentDeck);
        $newRoom = array_splice($currentDeck, 0, 4);

        $game->update([
            'deck' => $currentDeck,
            'current_room' => $newRoom,
            'can_flee' => false,
            'drank_potion' => false,
        ]);

        $history = session()->get('combat_history', []);
        array_unshift($history, "You fled! The room is reshuffled into the deck.");
        session(['combat_history' => $history]);

        session()->flash('animate', true);

        return redirect()->route('game.show', $game->id);
    }

    private function fillRoom($game)
    {
        $deck = $game->deck;
        $needed = 4 - count($game->current_room);
        $newCards = array_splice($deck, 0, $needed);

        $game->current_room = array_merge($game->current_room, $newCards);
        $game->deck = $deck;
        $game->can_flee = true;
        $game->drank_potion = false;

        session()->flash('animate', true);
    }

    private function handleMonster($game, $card)
    {
        $monsterValue = $card['value'];
        $suitIcon = ($card['suit'] === 'spades') ? '♠' : '♣';
        $weaponValue = $game->weapon_val ?? 0;

        $canUseWeapon = $game->weapon_val &&
                        ($game->last_slain_val === null || $monsterValue < $game->last_slain_val);

        if ($canUseWeapon) {
            $damage = max(0, $monsterValue - $weaponValue);
            $game->last_slain_val = $monsterValue;
            $msg = "Fought {$monsterValue}{$suitIcon} with weapon. Took {$damage} damage.";
        } else {
            $damage = $monsterValue;
            $brokenMsg = $game->weapon_val ? " Your weapon shattered!" : "";
            $game->weapon_val = null;
            $game->last_slain_val = null;
            $msg = "Fought {$monsterValue}{$suitIcon} barehanded. Took {$damage} damage.{$brokenMsg}";
        }

        $game->health -= $damage;
        return $msg;
    }

    private function handlePotion($game, $card)
    {
        if (!$game->drank_potion) {
            $game->health = min(20, $game->health + $card['value']);
            $game->drank_potion = true;
            return "Drank a potion of value {$card['value']}♥. Feeling restored.";
        }
        return "You tried to drink a potion, but you're already bloated! (Effect wasted)";
    }

    private function handleWeapon($game, $card)
    {
        $game->weapon_val = $card['value'];
        $game->last_slain_val = null;
        return "Equipped a new weapon: Value {$card['value']}♦.";
    }

}
