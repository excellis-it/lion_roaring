<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\SubscriptionPayment;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Stripe;

class SubscriptionController extends Controller
{
    // Deprecated — user subscription module removed.
    public function __construct()
    {
        // All methods intentionally disabled.
    }

    public function __call($method, $args)
    {
        abort(410, 'Subscription module removed.');
    }
}
