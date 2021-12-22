<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'transbank/webpay/mall/response',
        'transbank/final',
        'transbank/webpay/plus/response',
        'transbank/webpay/plus/order/response',
        'transbank/plus/final',
        'admin/payment/subscription/result',
        'payment/subscription/result',
    ];
}
