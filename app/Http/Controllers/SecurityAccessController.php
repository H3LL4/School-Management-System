<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Security & Access screen (IT / Support only):
 * create account, edit role, unlock, reset password, delete.
 */
class SecurityAccessController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->q . '%';
                $q->where(fn ($w) => $w->where('name', 'like', $term)->orWhere('email', 'like', $term));
            })
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->role))
            ->when($request->status === 'locked', fn ($q) => $q->where('locked_until', '>', now()))
            ->when($request->status === 'active', fn ($q) => $q->where(fn ($w) => $w->whereNull('locked_until')->orWhere('locked_until', '<=', now())))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('security.index', compact('users'));
    }

    public function create()
    {
        return view('security.form', ['user' => new User(['role' => 'staff'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'role'     => ['required', Rule::in(array_keys(User::ROLES))],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([...$data, 'password' => Hash::make($data['password'])]);

        ActivityLog::record('create', "Created account {$user->email} ({$user->role})");

        return redirect()->route('security.index')->with('success', 'Account created.');
    }

    public function edit(User $user)
    {
        return view('security.form', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        // Nobody can change their own role (prevents locking yourself out).
        // The role dropdown is disabled for your own row, so fill it in here.
        if ($user->id === auth()->id()) {
            $request->merge(['role' => $user->role]);
        }

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'role'  => ['required', Rule::in(array_keys(User::ROLES))],
        ]);

        $user->update($data);

        ActivityLog::record('update', "Updated account {$user->email} ({$user->role})");

        return redirect()->route('security.index')->with('success', 'Account updated.');
    }

    public function unlock(User $user): RedirectResponse
    {
        $user->update(['failed_attempts' => 0, 'locked_until' => null]);

        ActivityLog::record('unlock', "Unlocked account {$user->email}");

        return back()->with('success', "{$user->name} can sign in again.");
    }

    public function passwordForm(User $user)
    {
        return view('security.password', compact('user'));
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['password' => ['required', 'string', 'min:8', 'confirmed']]);

        // A reset also clears any lock.
        $user->update([
            'password'        => Hash::make($data['password']),
            'failed_attempts' => 0,
            'locked_until'    => null,
        ]);

        ActivityLog::record('password_reset', "Reset password for {$user->email}");

        return redirect()->route('security.index')->with('success', 'Password reset for ' . $user->name . '.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Log first; the log keeps the name even after the account is gone.
        ActivityLog::record('delete', "Deleted account {$user->email}");

        $user->delete();

        return back()->with('success', 'Account deleted.');
    }
}
