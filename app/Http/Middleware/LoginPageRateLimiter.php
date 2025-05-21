<?php

namespace App\Http\Middleware;

use App\Services\UserActivityLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class LoginPageRateLimiter
{
    // Length of the User-Agent hash prefix used in the rate limit key
    private const USER_AGENT_HASH_LENGTH = 12;

    private readonly bool $enabled;
    private readonly int $maxAttempts;
    private readonly int $hitDecay;

    /**
     * Creates a new LoginPageRateLimiter instance.
     *
     * Initializes rate limiting thresholds from configuration.
     */
    public function __construct()
    {
        $env = config('app.env');
        $enableLocally = config('auth.login_page_access_rate_limit.enable_locally');

        $this->enabled = $env === 'demo' || ($env === 'local' && $enableLocally);
        $this->maxAttempts = config('auth.login_page_access_rate_limit.max_attempts');
        $this->hitDecay = config('auth.login_page_access_rate_limit.decay_seconds');
    }

    /**
     * Throttles repeated access to the login page based on IP address.
     *
     * @param Request $request The current HTTP request
     * @param Closure $next    The next middleware handler
     *
     * @return Response A 429 response if too many attempts were made,
     *                  or the response from the next middleware/controller
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->enabled) {
            $key = $this->throttleKey($request);

            if (RateLimiter::tooManyAttempts($key, $this->maxAttempts)) {
                app(UserActivityLogger::class)->warning(
                    'Login page access blocked due to too many requests',
                    ['throttle_key' => $key]
                );

                $waitSeconds = RateLimiter::availableIn($key);

                return $this->buildLockoutResponse($waitSeconds);
            }

            RateLimiter::hit($key, $this->hitDecay);
        }

        return $next($request);
    }

    /**
     * Generate the rate-limiting key for this login attempt.
     *
     * @param Request $request
     * @return string The key used for throttling (client IP + hash of User Agent)
     */
    private function throttleKey(Request $request): string
    {
        $ip = $request->ip();
        $userAgentHash = substr(md5($request->userAgent()), 0, self::USER_AGENT_HASH_LENGTH);
        return "login-page:{$ip}:{$userAgentHash}";
    }

    /**
    * Build the HTTP response shown when the user is locked out due to rate limiting.
    *
    * @param int $waitSeconds Number of seconds remaining before the user may retry
    * @return Response A styled HTML response with status 429.
    */
    private function buildLockoutResponse(int $waitSeconds): Response
    {
        $pluralized = $waitSeconds === 1 ? 'second' : 'seconds';

        return response(
            <<<HTML
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Rate Limited</title>
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <style>
                    body {
                        font-family: sans-serif;
                        background-color: #1a202c;
                        color: #e2e8f0;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                    }
                    .card {
                        background-color: #2d3748;
                        padding: 2rem;
                        border-radius: 0.5rem;
                        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                        text-align: center;
                        max-width: 24rem;
                    }
                    .card h2 {
                        color: #f56565;
                        margin-bottom: 1rem;
                    }
                </style>
            </head>
            <body>
                <div class="card">
                    <h2>Too Many Requests</h2>
                    <p>You’ve hit the login page too many times in a short period.</p>
                    <p>Please try again in {$waitSeconds} {$pluralized}.</p>
                </div>
            </body>
            </html>
            HTML,
            Response::HTTP_TOO_MANY_REQUESTS
        );
    }
}
