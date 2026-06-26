<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laravel\Socialite\Socialite;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }
    /**
     * Handle login request.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');
        if (! Auth::attempt($credentials, $remember)) {
            return back()->withErrors([
                'email' => 'Email atau password yang Anda masukkan salah.',
            ])->onlyInput('email');
        }
        $user = Auth::user();
        if (! $user->is_active) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.',
            ])->onlyInput('email');
        }
        $request->session()->regenerate();
        return redirect()->route('dashboard');
    }
    /**
     * Handle logout request.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if they exist by email
            $user = User::where('email', $googleUser->email)->first();
            
            if (!$user) {
                return redirect('/login')->withErrors(['email' => 'No account associated with this Google email. Please contact the administrator.']);
            } 
            Auth::login($user);
            return redirect()->intended('dashboard');

        } catch (Exception $e) {
            return redirect('/login')->withErrors(['email' => 'Failed to login with Google. Please try again.']);
        }
    }
}