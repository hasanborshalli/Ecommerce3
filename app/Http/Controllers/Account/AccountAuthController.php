<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerPasswordResetToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountAuthController extends Controller
{
    // ── Login ─────────────────────────────────────────────────

    public function showLogin()
    {
        return view('account.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $customer = Customer::where('email', $validated['email'])->first();

        if (!$customer || !Hash::check($validated['password'], $customer->password)) {
            return back()
                ->withInput(['email' => $validated['email']])
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        // Regenerate session to prevent fixation
        $request->session()->regenerate();

        session([
            'customer_id'         => $customer->id,
            'customer_name'       => $customer->full_name,
            'customer_first_name' => $customer->first_name,
            'customer_email'      => $customer->email,
            'customer_phone'      => $customer->phone,
        ]);

        // Merge guest cart into session (cart already there, nothing to merge for now)
        // Full cart-merge hook can be added in Step 6 if needed.

        $intended = session()->pull('intended', route('account.dashboard'));

        return redirect($intended)
            ->with('success', 'Welcome back, ' . $customer->first_name . '!');
    }

    // ── Register ──────────────────────────────────────────────

    public function showRegister()
    {
        return view('account.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name'            => 'required|string|max:60',
            'last_name'             => 'required|string|max:60',
            'email'                 => 'required|email|max:150|unique:customers,email',
            'phone'                 => 'nullable|string|max:30',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        $customer = Customer::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'phone'      => $validated['phone'] ?? null,
            'password'   => Hash::make($validated['password']),
        ]);

        $request->session()->regenerate();

        session([
            'customer_id'         => $customer->id,
            'customer_name'       => $customer->full_name,
            'customer_first_name' => $customer->first_name,
            'customer_email'      => $customer->email,
            'customer_phone'      => $customer->phone,
        ]);

        return redirect()->route('account.dashboard')
            ->with('success', 'Welcome to ' . config('app.name') . ', ' . $customer->first_name . '!');
    }

    // ── Logout ────────────────────────────────────────────────

    public function logout(Request $request)
    {
        $request->session()->forget([
            'customer_id',
            'customer_name',
            'customer_first_name',
            'customer_email',
            'customer_phone',
        ]);

        return redirect()->route('home')
            ->with('success', 'You have been signed out.');
    }

    // ── Forgot password ───────────────────────────────────────

    public function showForgot()
    {
        return view('account.forgot-password');
    }

    public function sendReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $customer = Customer::where('email', $request->email)->first();

        // Always show success to prevent email enumeration
        if (!$customer) {
            return back()->with('status', 'If that email is registered, we\'ve sent a reset link.');
        }

        $token = Str::random(64);

        DB::table('customer_password_reset_tokens')->updateOrInsert(
            ['email' => $customer->email],
            [
                'token'      => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // In production: dispatch a mail here.
        // For the template we store the raw token in session so the buyer can demo the flow.
        session(['_demo_reset_token_' . $customer->email => $token]);

        return back()->with('status', 'If that email is registered, we\'ve sent a reset link.');
    }

    // ── Reset password ────────────────────────────────────────

    public function showReset(Request $request, string $token)
    {
        return view('account.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token'    => 'required|string',
            'email'    => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $record = DB::table('customer_password_reset_tokens')
            ->where('email', $validated['email'])
            ->first();

        if (!$record || !Hash::check($validated['token'], $record->token)) {
            return back()->withErrors(['email' => 'This reset link is invalid or has expired.']);
        }

        // Tokens expire after 60 minutes
        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('customer_password_reset_tokens')->where('email', $validated['email'])->delete();
            return back()->withErrors(['email' => 'This reset link has expired. Please request a new one.']);
        }

        $customer = Customer::where('email', $validated['email'])->first();

        if (!$customer) {
            return back()->withErrors(['email' => 'Account not found.']);
        }

        $customer->update(['password' => Hash::make($validated['password'])]);

        DB::table('customer_password_reset_tokens')->where('email', $validated['email'])->delete();

        return redirect()->route('account.login')
            ->with('success', 'Password reset successfully. Please sign in.');
    }
}
