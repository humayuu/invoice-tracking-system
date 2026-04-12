<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Throwable;

class GoogleLoginController extends Controller
{
    public const SESSION_LINK_INTENT = 'google_oauth_linking';

    /**
     * Guest OAuth redirect (stateless; pairs with stateless callback branch).
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    /**
     * Authenticated user: connect Google to this account (session-based OAuth state).
     */
    public function redirectToConnectGoogle(Request $request)
    {
        if ($request->user()->google_id) {
            return redirect()
                ->route('profile')
                ->with('status', 'google-already');
        }

        session([self::SESSION_LINK_INTENT => true]);

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            if (session()->pull(self::SESSION_LINK_INTENT, false)) {
                return $this->handleLinkingCallback();
            }

            return $this->handleGuestCallback();
        } catch (InvalidStateException $e) {
            return $this->oauthFailed('Invalid or expired Google sign-in. Please try again.');
        } catch (Throwable $e) {
            report($e);

            return $this->oauthFailed('Could not sign in with Google. Please try again.');
        }
    }

    protected function oauthFailed(string $message)
    {
        return redirect()
            ->route('login')
            ->withErrors(['google' => $message]);
    }

    protected function handleLinkingCallback()
    {
        if (! Auth::check()) {
            return $this->oauthFailed('Your session expired. Please sign in and try again.');
        }

        $googleUser = Socialite::driver('google')->user();

        $email = $googleUser->getEmail();
        if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()
                ->route('profile')
                ->withErrors(['google' => 'Google did not provide a valid email address.']);
        }

        $user = Auth::user();

        if (strcasecmp($email, $user->email) !== 0) {
            return redirect()
                ->route('profile')
                ->withErrors([
                    'google' => 'The Google account email must match your profile email ('.$user->email.').',
                ]);
        }

        $googleId = $googleUser->getId();
        $existing = User::where('google_id', $googleId)
            ->where('id', '!=', $user->id)
            ->first();

        if ($existing) {
            return redirect()
                ->route('profile')
                ->withErrors(['google' => 'This Google account is already connected to another user.']);
        }

        $user->forceFill([
            'google_id' => $googleId,
            'google_avatar' => $googleUser->getAvatar(),
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        return redirect()
            ->route('profile')
            ->with('status', 'google-connected');
    }

    protected function handleGuestCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $email = $googleUser->getEmail();
        if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->oauthFailed('Google did not return a valid email address for your account.');
        }

        $name = $googleUser->getName();
        if (! is_string($name) || trim($name) === '') {
            $name = strstr($email, '@', true) ?: 'User';
        }

        $googleId = $googleUser->getId();

        $user = User::where('google_id', $googleId)->first();

        if ($user) {
            $user->forceFill([
                'google_avatar' => $googleUser->getAvatar(),
            ])->save();
        } else {
            if (User::where('email', $email)->exists()) {
                return $this->oauthFailed(
                    'An account with this email already exists. Sign in with your password, open Profile, and use “Connect Google”.'
                );
            }

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'google_id' => $googleId,
                'google_avatar' => $googleUser->getAvatar(),
                'password' => Str::password(32),
                'created_with_google' => true,
            ]);

            event(new Registered($user));
        }

        Auth::login($user);

        return redirect()->intended(route('dashboard'));
    }
}
