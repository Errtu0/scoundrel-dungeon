# 🃏 Scoundrel: The Dungeon Depth

A dark-fantasy, solo dungeon crawler built with **Laravel**, **Tailwind CSS**, and **Alpine.js**. Inspired by the roguelike card game *Scoundrel*.

## 🕯️ The Premise
You are a rogue trapped in a 52-card dungeon. Your goal is to clear the entire deck without your health reaching zero.

### ⚔️ The Rules of the Void
The dungeon consists of a deck of standard playing cards, where suits determine your fate:
* **Spades & Clubs (Monsters):** Enemies you must fight. If you have a weapon, you can mitigate damage, but only if the monster is weaker than your previous kill!
* **Diamonds (Weapons):** Equipping a diamond card gives you an attack value. Using a weapon against a monster might break it if you aren't careful.
* **Hearts (Potions):** Heals your vitality. Careful—you can only drink one potion per room.

## 🕹️ Features
* **Dynamic Combat Log:** A scrolling "Chronicle of the Void" that tracks every move, hit, and heal.
* **Atmospheric UI:** Built with a medieval aesthetic, featuring ember animations and blood-vignette health warnings.
* **Tactical Fleeing:** Once per room, you can scramble back into the darkness to reshuffle the current encounter.

## 🛠️ Tech Stack
* **Backend:** Laravel 11
* **Frontend:** Tailwind CSS, Alpine.js
* **Icons:** RPG-Awesome
* **Deployment:** Laravel Cloud

## 🚀 Local Installation
1. Clone the repo: `git clone https://github.com/YOUR_USERNAME/scoundrel-dungeon.git`
2. Install dependencies: `composer install`
3. Set up environment: `cp .env.example .env && php artisan key:generate`
4. Run migrations: `php artisan migrate`
5. Start the engine: `php artisan serve`

---
*Developed as a dark-fantasy web experience.*
