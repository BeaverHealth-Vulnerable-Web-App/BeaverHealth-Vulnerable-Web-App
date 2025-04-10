<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;

class TrustProxiesMiddleware extends TrustProxies
{
    /**
     * The trusted proxies for this application.
     *
     * In production (when the app is behind a load balancer), trust all proxies so
     * Laravel uses the X-Forwarded-* headers to determine the original client IP.
     *
     * In local/dev environments, don't trust any proxies to avoid inaccurate IP detection.
     *
     * @var array|string|null
     */
    protected $proxies = config('app.env') === 'prod' ? '*' : null;

    /**
     * The headers that should be used to detect proxies.
     *
     * Set by GCP's load balancer. Laravel will only use them when the request comes through
     * a trusted proxy (see $proxies).
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR |
                         Request::HEADER_X_FORWARDED_HOST |
                         Request::HEADER_X_FORWARDED_PORT |
                         Request::HEADER_X_FORWARDED_PROTO;
}
