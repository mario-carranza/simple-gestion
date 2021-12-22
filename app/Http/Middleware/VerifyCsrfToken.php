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
        'transbank/webpay/mall/order/response',
        'transbank/final',
        'admin/payment/subscription/result',
        'payment/subscription/result',
    ];
}
