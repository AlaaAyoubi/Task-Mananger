<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        
        $user = Auth::user();

        if (!$user) {
            return redirect('/dashboard');
        }

        // التحقق من دور الأدمن أولاً (الأولوية العليا)
        if ($user->hasRole('admin') || $user->teams()->wherePivot('role', 'admin')->exists()) {
            return redirect()->route('admin.dashboard');
        }
        
        // ثم التحقق من دور المدير
        if ($user->hasRole('manager') || $user->teams()->wherePivot('role', 'manager')->exists()) {
            return redirect()->route('manager.dashboard');
        }
        
        // وأخيراً دور العضو
        if ($user->hasRole('member') || $user->teams()->wherePivot('role', 'member')->exists()) {
            return redirect()->route('member.dashboard');
        }
        
        // احتياط
        return redirect('/dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
