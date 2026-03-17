<?php

namespace App\Services;

class CardService
{
    public function generateScoundrelDeck(): array
    {
        $suits = ['spades', 'clubs', 'hearts', 'diamonds'];
        $values = [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]; // 11-14 = J, Q, K, A
        $deck = [];

        foreach ($suits as $suit) {
            foreach ($values as $value) {
                // Remove Red Face cards and Red Aces
                if (in_array($suit, ['hearts', 'diamonds']) && $value >= 11) {
                    continue;
                }
                $deck[] = ['suit' => $suit, 'value' => $value];
            }
        }

        shuffle($deck);
        return $deck;
    }
}
