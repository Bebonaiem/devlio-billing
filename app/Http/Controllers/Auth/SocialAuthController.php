<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class SocialAuthController extends Controller
{
    protected array $providers = ['google', 'github', 'discord'];

    public function redirect(string $provider): RedirectResponse
    {
        if (! in_array($provider, $this->providers)) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        if (! in_array($provider, $this->providers)) {
            abort(404);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect('/login')->withErrors([
                'email' => 'Unable to authenticate with '.ucfirst($provider).'. Please try again.',
            ]);
        }

        $existingUser = User::where('email', $socialUser->getEmail())->first();

        if ($existingUser) {
            if ($existingUser->provider === $provider) {
                Auth::login($existingUser, false);

                return redirect()->intended(route('dashboard.index'));
            }

            return redirect()->route('login')->withErrors([
                'email' => 'This email is already registered. Please sign in with your password.',
            ]);
        }

        $user = User::create([
            'first_name' => $socialUser->getName() ?? strtok($socialUser->getEmail(), '@'),
            'last_name' => '',
            'name' => $socialUser->getName() ?? strtok($socialUser->getEmail(), '@'),
            'email' => $socialUser->getEmail(),
            'password' => Hash::make(Str::random(32)),
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'email_verified_at' => now(),
        ]);

        if (Role::where('name', 'customer')->exists()) {
            $user->assignRole('customer');
        }

        Auth::login($user, false);

        return redirect()->intended(route('dashboard.index'));
    }
}
