<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User Shipping Provider Selection
    |--------------------------------------------------------------------------
    |
    | Allow users to choose their preferred shipping provider during checkout.
    | If disabled, the system will automatically select based on criteria.
    |
    */

    'allow_user_selection' => env('SHIPPING_ALLOW_USER_SELECTION', true),

    /*
    |--------------------------------------------------------------------------
    | Auto-Selection Criteria
    |--------------------------------------------------------------------------
    |
    | When auto-selecting shipping provider, use this criteria:
    | - cheapest: Select provider with lowest fee
    | - fastest: Select provider with shortest delivery time
    | - balanced: Balance between price and speed (60% price, 40% speed)
    |
    */

    'auto_select_criteria' => env('SHIPPING_AUTO_SELECT_CRITERIA', 'cheapest'),

    /*
    |--------------------------------------------------------------------------
    | Show Provider Details
    |--------------------------------------------------------------------------
    |
    | Show detailed information about shipping providers to users:
    | - Provider name and logo
    | - Estimated delivery time
    | - Shipping fee breakdown
    |
    */

    'show_provider_details' => env('SHIPPING_SHOW_PROVIDER_DETAILS', true),

    /*
    |--------------------------------------------------------------------------
    | Cache Duration
    |--------------------------------------------------------------------------
    |
    | Cache shipping fee calculations to improve performance.
    | Duration in seconds. Set to 0 to disable caching.
    |
    */

    'cache_duration' => env('SHIPPING_CACHE_DURATION', 300), // 5 minutes

    /*
    |--------------------------------------------------------------------------
    | Minimum Order Value for Free Shipping
    |--------------------------------------------------------------------------
    |
    | Orders above this value get free shipping.
    | Set to null to disable free shipping.
    |
    */

    'free_shipping_threshold' => env('SHIPPING_FREE_THRESHOLD', null),
];
