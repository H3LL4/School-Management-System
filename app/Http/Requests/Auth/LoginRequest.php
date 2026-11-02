<?php

namespace App\Http\Requests\Auth;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to sign in, applying the account-lockout policy:
     *  - locks after N consecutive failed attempts (config/school.php)
     *  - lock releases after M minutes, or IT/Support unlocks it
     *  - the counter resets on every successful login
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $max     = config('school.login_max_failed_attempts');
        $minutes = config('school.login_lockout_minutes');
        $email   = (string) $this->input('email');
        $user    = User::where('email', $email)->first();

        if ($user && $user->isLocked()) {
            // Even the correct password is refused while the account is locked.
            throw ValidationException::withMessages([
                'email' => 'This account is locked. Try again in ' . $user->lockMinutesLeft()
                    . ' minute(s), or ask IT/Support to unlock it.',
            ]);
        }

        // An old lock has expired: start fresh.
        if ($user && $user->locked_until && $user->locked_until->isPast()) {
            $user->update(['failed_attempts' => 0, 'locked_until' => null]);
        }

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            if ($user) {
                $attempts = $user->failed_attempts + 1;
                ActivityLog::record('failed_login', "Failed sign-in for {$email} (attempt {$attempts})", $user);

                if ($attempts >= $max) {
                    $user->update(['failed_attempts' => $attempts, 'locked_until' => now()->addMinutes($minutes)]);
                    ActivityLog::record('locked', "Account locked after {$attempts} failed attempts: {$email}", $user);

                    throw ValidationException::withMessages([
                        'email' => "Too many failed attempts. This account is locked for {$minutes} minutes.",
                    ]);
                }

                $user->update(['failed_attempts' => $attempts]);
                $left = $max - $attempts;

                throw ValidationException::withMessages([
                    'email' => "Incorrect email or password. {$left} attempt(s) left before the account is locked.",
                ]);
            }

            throw ValidationException::withMessages(['email' => trans('auth.failed')]);
        }

        RateLimiter::clear($this->throttleKey());

        // Successful login: reset the counter.
        if ($user && ($user->failed_attempts > 0 || $user->locked_until)) {
            $user->update(['failed_attempts' => 0, 'locked_until' => null]);
        }
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 10)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}
