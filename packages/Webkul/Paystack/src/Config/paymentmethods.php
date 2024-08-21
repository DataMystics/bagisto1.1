<?php

return [

    'paystack_standard' => [
        'code'             => 'paystack_standard',
        'title'            => 'paystack Standard',
        'description'      => 'paystack Standard',
        'class'            => 'Webkul\paystack\Payment\Standard',
        'sandbox'          => true,
        'active'           => true,
        'business_account' => 'test@webkul.com',
        'sort'             => 3,
    ],
];
