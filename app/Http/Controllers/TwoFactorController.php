<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TotpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    private TotpService $totpService;

    public function __construct(TotpService $totpService)
    {
        $this->totpService = $totpService;
    }

    public function challenge()
    {
        if (! session('tfa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.2fa-challenge');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $userId = session('tfa_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (! $user || ! $user->tfa_secret || ! $this->totpService->verifyCode($user->tfa_secret, $request->code)) {
            return back()->withErrors([
                'code' => 'The verification code is invalid. Please try again.',
            ])->withInput();
        }

        Auth::login($user, false);
        session()->forget('tfa_user_id');
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard.index'));
    }

    public function show()
    {
        $user = Auth::user();
        $secret = $user->tfa_secret;

        if (! $secret) {
            $secret = $this->totpService->generateSecret();
            session(['pending_tfa_secret' => $secret]);
        }

        $qrCodeUrl = $this->totpService->getQrCodeUrl(
            $secret,
            $user->email,
            config('app.name', 'Billing')
        );

        return view('dashboard.two-factor', [
            'user' => $user,
            'secret' => $secret,
            'qrCodeUrl' => $qrCodeUrl,
            'formattedSecret' => $this->totpService->formatSecret($secret),
            'isEnabled' => ! empty($user->tfa_secret),
        ]);
    }

    public function enable(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:6',
            'secret' => 'required|string',
        ]);

        $code = $validated['code'];
        $secret = $validated['secret'];

        if (! $this->totpService->verifyCode($secret, $code)) {
            return back()->withErrors([
                'code' => 'The verification code is invalid. Please try again.',
            ]);
        }

        Auth::user()->update(['tfa_secret' => $secret]);
        session()->forget('pending_tfa_secret');

        return back()->with('success', 'Two-factor authentication has been enabled.');
    }

    public function disable(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        if (! $user->tfa_secret) {
            return back()->withErrors([
                'code' => 'Two-factor authentication is not enabled.',
            ]);
        }

        if (! $this->totpService->verifyCode($user->tfa_secret, $validated['code'])) {
            return back()->withErrors([
                'code' => 'The verification code is invalid. Please try again.',
            ]);
        }

        $user->update(['tfa_secret' => null]);

        return back()->with('success', 'Two-factor authentication has been disabled.');
    }
}
