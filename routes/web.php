<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\ExpeditionController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\InfernalTowerController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\LevelRewardController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\MeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OpeningController;
use App\Http\Controllers\PokedexController;
use App\Http\Controllers\PokemonUpgradeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SuccessController;
use App\Http\Controllers\UserFriendGiftController;
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
    Route::get('/profile/totp', [AuthenticatedSessionController::class, 'setupTotp'])->name('totp.setup');
    Route::post('/profile/totp/enable', [AuthenticatedSessionController::class, 'enableTotp'])->name('totp.enable');
    Route::post('/profile/totp/disable', [AuthenticatedSessionController::class, 'disableTotp'])->name('totp.disable');

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
    Route::post('/promocodes/use', [PromoCodeController::class, 'useCode'])->name('promocodes.use');

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

    // Routes pour Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');

    // Route pour la Tour Infernale
    Route::get('/tower', [InfernalTowerController::class, 'index'])->name('tower.index');

    // Routes pour les amis
    Route::get('/friends', [FriendController::class, 'index'])->name('friends.index');
    Route::post('/friends/send-request', [FriendController::class, 'sendRequest'])->name('friends.sendRequest');
    Route::post('/friends/accept-request', [FriendController::class, 'acceptRequest'])->name('friends.acceptRequest');
    Route::post('/friends/remove', [FriendController::class, 'remove'])->name('friends.remove');
    Route::get('/friends/search', [FriendController::class, 'search'])->name('friends.search');

    // Routes pour les cadeaux d'amis
    Route::post('/friend-gifts/send', [UserFriendGiftController::class, 'send'])->name('friend-gifts.send');
    Route::post('/friend-gifts/claim', [UserFriendGiftController::class, 'claim'])->name('friend-gifts.claim');
    Route::post('/friend-gifts/claim-all', [UserFriendGiftController::class, 'claimAll'])->name('friend-gifts.claim-all');
    Route::post('/friend-gifts/send-all', [UserFriendGiftController::class, 'sendToAll'])->name('friend-gifts.send-all');

    // Routes pour les Clubs
    Route::get('/clubs', [ClubController::class, 'index'])->name('club.index');
    Route::get('/clubs/create', [ClubController::class, 'create'])->name('club.create');
    Route::post('/clubs', [ClubController::class, 'store'])->name('club.store');
    Route::get('/clubs/{club}', [ClubController::class, 'show'])->name('club.show');
    Route::post('/clubs/{club}/join', [ClubController::class, 'join'])->name('club.join');
    Route::post('/clubs/leave', [ClubController::class, 'leave'])->name('club.leave');
    Route::post('/club-requests/{request}/accept', [ClubController::class, 'acceptRequest'])->name('club.accept-request');
    Route::post('/club-requests/{request}/reject', [ClubController::class, 'rejectRequest'])->name('club.reject-request');
    Route::post('/clubs/{club}/remove-member', [ClubController::class, 'removeMember'])->name('club.remove-member');
    Route::post('/clubs/{club}/transfer-leadership', [ClubController::class, 'transferLeadership'])->name('club.transfer-leadership');
    Route::delete('/clubs/{club}', [ClubController::class, 'destroyClub'])->name('club.destroy');

});

require __DIR__ . '/admin.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/api.php';
