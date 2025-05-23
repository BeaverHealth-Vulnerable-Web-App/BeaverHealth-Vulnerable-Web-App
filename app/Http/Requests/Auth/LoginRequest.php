<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Services\UserActivityLogger;

class LoginRequest extends FormRequest
{
    private readonly int $rateLimitMaxAttempts;
    private readonly int $rateLimitHitDecay;

    /**
     * Create a new LoginRequest instance and initialize rate limiting settings.
     */
    public function __construct()
    {
        $this->rateLimitMaxAttempts = config('auth.login_attempts_rate_limit.max_attempts');
        $this->rateLimitHitDecay = config('auth.login_attempts_rate_limit.decay_seconds');
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool True if the request is authorized
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array{username: array<int, string>, password: array<int, string>} An array of validation rules
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the user without rate limiting.
     *
     * @return void
     *
     * @throws ValidationException If authentication fails
     */
    public function authenticate(): void
    {
        if (!Auth::attempt($this->only('username', 'password'))) {
            $this->logFailedLoginAttempt();
            throw ValidationException::withMessages(['username' => trans('auth.failed')]);
        }
    }

    /**
     * Attempt to authenticate the user with rate limiting.
     *
     * @return void
     *
     * @throws ValidationException If the login fails or rate-limit is exceeded
     */
    public function authenticateOrThrottle(): void
    {
        $this->ensureIsNotRateLimited();

        if (!Auth::attempt($this->only('username', 'password'))) {
            RateLimiter::hit($this->throttleKey(), $this->rateLimitHitDecay);
            $this->logFailedLoginAttempt();
            throw ValidationException::withMessages(['username' => trans('auth.failed')]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure that the request is not rate-limited.
     *
     * If the limit has been exceeded, dispatches a lockout and throws a validation exception.
     *
     * @return void
     *
     * @throws ValidationException If too many attempts have been made
     */
    private function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), $this->rateLimitMaxAttempts)) {
            return;
        }

        app(UserActivityLogger::class)->warning(
            'Login attempt blocked due to too many failed login attempts',
            ['throttle_key' => $this->throttleKey()]
        );

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages(
            [
                'username' => trans(
                    'auth.throttle',
                    [
                        'seconds' => $seconds,
                        'minutes' => ceil($seconds / 60),
                    ]
                ),
            ]
        );
    }

    /**
     * Generate the rate-limiting key for this login attempt.
     *
     * @return string The key used for throttling (username + client IP)
     */
    private function throttleKey(): string
    {
        return Str::lower($this->string('username') . '|' . $this->ip());
    }

    /**
     * Log a failed login attempt for the current throttle key.
     */
    private function logFailedLoginAttempt(): void
    {
        $key = $this->throttleKey();

        app(UserActivityLogger::class)->info('Failed login attempt', [
            'attempts' => RateLimiter::attempts($key),
            'throttle_key' => $key,
        ]);
    }
}
