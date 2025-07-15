<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to OAuth provider
     */
    public function redirect(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle OAuth provider callback
     */
    public function callback(string $provider): RedirectResponse
    {
        try {
            $this->validateProvider($provider);

            $socialUser = Socialite::driver($provider)->user();

            $user = $this->findExistingSocialUser($provider, $socialUser);

            if ($user) {
                $this->updateSocialUserData($user, $provider, $socialUser);
                Auth::login($user);
                return $this->redirectAfterLogin($user->fresh());
            }

            $existingUser = User::where('email', $socialUser->getEmail())->first();

            if ($existingUser) {
                $this->linkSocialAccount($existingUser, $provider, $socialUser);
                Auth::login($existingUser);
                return $this->redirectAfterLogin($existingUser->fresh());
            }

            $newUser = $this->createUserFromSocialData($provider, $socialUser);
            Auth::login($newUser);

            return $this->redirectAfterLogin($newUser->fresh());

        } catch (Exception $e) {
            logger()->error('Social auth error', [
                'provider' => $provider,
                'error' => $e->getMessage()
            ]);

            return redirect('/login')->with('status', 'Erreur lors de la connexion avec ' . ucfirst($provider) . '. Veuillez réessayer.');
        }
    }

    /**
     * Validate the OAuth provider
     */
    private function validateProvider(string $provider): void
    {
        if (!in_array($provider, ['google', 'discord'])) {
            abort(404, 'Provider non supporté');
        }
    }

    /**
     * Find existing user with social account
     */
    private function findExistingSocialUser(string $provider, $socialUser): ?User
    {
        $field = $provider . '_id';
        return User::where($field, $socialUser->getId())->first();
    }

    /**
     * Update social user data
     */
    private function updateSocialUserData(User $user, string $provider, $socialUser): void
    {
        $updateData = [
            'last_login' => now(),
            'provider_verified_at' => now(),
            'email_verified_at' => now(),
        ];

        if ($provider === 'google') {
            $updateData['google_avatar'] = $socialUser->getAvatar();
        } elseif ($provider === 'discord') {
            $updateData['discord_avatar'] = $socialUser->getAvatar();
            $updateData['discord_username'] = $socialUser->getNickname() ?? $socialUser->getName();
        }

        $user->update($updateData);
    }

    /**
     * Link social account to existing user
     */
    private function linkSocialAccount(User $user, string $provider, $socialUser): void
    {
        $updateData = [
            'last_login' => now(),
            'provider_verified_at' => now(),
            'email_verified_at' => now(),
        ];

        if ($provider === 'google') {
            $updateData['google_id'] = $socialUser->getId();
            $updateData['google_avatar'] = $socialUser->getAvatar();
        } elseif ($provider === 'discord') {
            $updateData['discord_id'] = $socialUser->getId();
            $updateData['discord_username'] = $socialUser->getNickname() ?? $socialUser->getName();
            $updateData['discord_avatar'] = $socialUser->getAvatar();
        }

        if (!$user->provider) {
            $updateData['provider'] = $provider;
        }

        $user->update($updateData);
    }

    /**
     * Create new user from social data
     */
    private function createUserFromSocialData(string $provider, $socialUser): User
    {
        $username = $this->generateUniqueUsername($socialUser, $provider);

        $userData = [
            'username' => $username,
            'email' => $socialUser->getEmail(),
            'email_verified_at' => now(),
            'provider' => $provider,
            'provider_verified_at' => now(),
            'last_login' => now(),
            'avatar' => '/images/trainer/1.png',
            'unlocked_avatars' => ['/images/trainer/1.png', '/images/trainer/2.png'],
        ];

        if ($provider === 'google') {
            $userData['google_id'] = $socialUser->getId();
            $userData['google_avatar'] = $socialUser->getAvatar();
        } elseif ($provider === 'discord') {
            $userData['discord_id'] = $socialUser->getId();
            $userData['discord_username'] = $socialUser->getNickname() ?? $socialUser->getName();
            $userData['discord_avatar'] = $socialUser->getAvatar();
        }

        $user = User::create($userData);
        $user->assignRole('user');

        return $user->fresh();
    }

    /**
     * Generate unique username from social data
     */
    private function generateUniqueUsername($socialUser, string $provider): string
    {
        $baseUsername = '';

        if ($provider === 'discord') {
            $baseUsername = $socialUser->getNickname() ?? $socialUser->getName();
        } else {
            $baseUsername = $socialUser->getName();
        }

        $username = Str::slug($baseUsername, '');
        $username = preg_replace('/[^a-zA-Z0-9]/', '', $username);
        $username = substr($username, 0, 20);

        if (empty($username)) {
            $username = $provider . 'user';
        }

        $originalUsername = $username;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $originalUsername . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Redirect after successful login
     */
    private function redirectAfterLogin(User $user): RedirectResponse
    {
        if (!$user->hasVerifiedEmail() && !$user->provider) {
            return redirect()->route('verification.notice');
        }

        return redirect()->intended(route('me', absolute: false));
    }
}
