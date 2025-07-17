<?php

use App\Http\Controllers\ExpeditionController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\LevelRewardController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\MeController;
use App\Http\Controllers\OpeningController;
use App\Http\Controllers\PokedexController;
use App\Http\Controllers\PokemonUpgradeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SuccessController;
use App\Http\Controllers\UserProfileController;
use App\Models\Pokedex;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Route pour Me
    Route::get('/me', [MeController::class, 'index'])->name('me');

    // Routes 2FA
    Route::get('/profile/totp', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'setupTotp'])->name('totp.setup');
    Route::post('/profile/totp/enable', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'enableTotp'])->name('totp.enable');
    Route::post('/profile/totp/disable', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'disableTotp'])->name('totp.disable');

    // Route pour voir le profil d'un utilisateur
    Route::get('/profile/{user:username}', [UserProfileController::class, 'show'])->middleware(['verified'])->name('user.profile.show');

    // Routes pour Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar.update');
    Route::patch('/background', [ProfileController::class, 'updateBackground'])->name('background.update');

    // Routes pour Pokedex
    Route::get('/pokedex/user-pokemons', [PokedexController::class, 'getUserPokemons'])->name('pokedex.user-pokemons');
    Route::post('/pokedex/{id}/add-to-team', [PokedexController::class, 'addToTeam'])->name('pokedex.add-to-team');
    Route::post('/pokedex/{id}/remove-from-team', [PokedexController::class, 'removeFromTeam'])->name('pokedex.remove-from-team');

    // Routes pour PromoCode
    Route::get('/promocodes', [PromoCodeController::class, 'index'])->name('promocodes.index');
    Route::get('/promocodes/create', [PromoCodeController::class, 'create'])->name('promocodes.create');
    Route::post('/promocodes', [PromoCodeController::class, 'store'])->name('promocodes.store');
    Route::post('/promocodes/use', [PromoCodeController::class, 'useCode'])->name('promocodes.use');
    Route::delete('/promocodes/{id}', [PromoCodeController::class, 'destroy'])->name('promocodes.destroy');

    // Routes pour Marketplace
    Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');
    Route::get('/marketplace/sell', [MarketplaceController::class, 'sell'])->name('marketplace.sell');
    Route::post('/marketplace/sell', [MarketplaceController::class, 'listPokemon'])->name('marketplace.sell.post');
    Route::post('/marketplace/buy/{listingId}', [MarketplaceController::class, 'buyPokemon'])->name('marketplace.buy');
    Route::post('/marketplace/cancel/{listingId}', [MarketplaceController::class, 'cancelListing'])->name('marketplace.cancel');
    Route::get('/marketplace/listings', [MarketplaceController::class, 'getListings'])->name('marketplace.listings');

    // Routes pour Pokemon Upgrade
    Route::get('/pokemon-upgrade', [PokemonUpgradeController::class, 'index'])->name('pokemon-upgrade.index');
    Route::post('/pokemon-upgrade/upgrade', [PokemonUpgradeController::class, 'upgrade'])->name('pokemon-upgrade.upgrade');

    // Routes pour Opening
    Route::get('/opening', [OpeningController::class, 'index'])->name('opening.index');

    // Routes pour Shop
    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
    Route::post('/shop/buy', [ShopController::class, 'buyItem'])->name('shop.buy');

    // Routes pour Success
    Route::get('/success', [SuccessController::class, 'index'])->name('success.index');
    Route::post('/success/{successId}/claim', [SuccessController::class, 'claim'])->name('success.claim');
    Route::post('/success/claim-all', [SuccessController::class, 'claimAll'])->name('success.claim-all');

    // Routes pour LevelRewards
    Route::post('/level-rewards/claim', [LevelRewardController::class, 'claim'])->name('level-rewards.claim');
    Route::post('/level-rewards/claim-all', [LevelRewardController::class, 'claimAll'])->name('level-rewards.claim-all');

    // Routes pour Expeditions
    Route::get('/expeditions', [ExpeditionController::class, 'index'])->name('expeditions.index');
    Route::get('/expeditions/{expedition}', [ExpeditionController::class, 'show'])->name('expeditions.show');

    // Routes pour Leaderboard
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');

    // Gestion des amis
    Route::get('/friends', [\App\Http\Controllers\FriendController::class, 'index'])->name('friends.index');
    Route::post('/friends/send-request', [\App\Http\Controllers\FriendController::class, 'sendRequest'])->name('friends.sendRequest');
    Route::post('/friends/accept-request', [\App\Http\Controllers\FriendController::class, 'acceptRequest'])->name('friends.acceptRequest');
    Route::post('/friends/remove', [\App\Http\Controllers\FriendController::class, 'remove'])->name('friends.remove');
    Route::get('/friends/search', [\App\Http\Controllers\FriendController::class, 'search'])->name('friends.search');

    // Gestion des cadeaux d'amis
    Route::post('/friend-gifts/send', [\App\Http\Controllers\UserFriendGiftController::class, 'send'])->name('friend-gifts.send');
    Route::post('/friend-gifts/claim', [\App\Http\Controllers\UserFriendGiftController::class, 'claim'])->name('friend-gifts.claim');
    Route::post('/friend-gifts/claim-all', [\App\Http\Controllers\UserFriendGiftController::class, 'claimAll'])->name('friend-gifts.claim-all');
    Route::post('/friend-gifts/send-all', [\App\Http\Controllers\UserFriendGiftController::class, 'sendToAll'])->name('friend-gifts.send-all');
});

require __DIR__ . '/admin.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/api.php';
