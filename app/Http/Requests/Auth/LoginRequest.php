<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Services\UserActivityLogger;

class LoginRequest extends FormRequest
{
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
     * Attempt to authenticate the user with the given credentials.
     *
     * If authentication fails or the request is rate-limited, a validation exception is thrown.
     *
     * @return void
     *
     * @throws ValidationException If the login fails or rate-limit is exceeded
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (!Auth::attempt($this->only('username', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());
            app(UserActivityLogger::class)->info('Failed login attempt', [
                'attempts' => RateLimiter::attempts($this->throttleKey()),
                'throttle_key' => $this->throttleKey(),
            ]);
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
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        app(UserActivityLogger::class)->warning(
            'Login attempt blocked due to too many failed login attempts',
            ['throttle_key' => $this->throttleKey()]
        );

        event(new Lockout($this));

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
    protected function throttleKey(): string
    {
        return Str::lower($this->string('username') . '|' . $this->ip());
    }
}
