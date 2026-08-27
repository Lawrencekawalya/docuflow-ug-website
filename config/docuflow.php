<?php

return [
    'contact' => [
        'email' => env('DOCUFLOW_CONTACT_EMAIL') ?: 'lawkawalya@gmail.com',
        'phone' => env('DOCUFLOW_PHONE') ?: '+256 755400297',
        'whatsapp' => env('DOCUFLOW_WHATSAPP_NUMBER') ?: '+256 778864614',
    ],

    'leads' => [
        'email' => env('DOCUFLOW_LEADS_EMAIL'),
    ],

    'pricing' => [
        'starter' => [
            'monthly' => 150000,
            'setup' => 500000,
            'allowance' => env('DOCUFLOW_STARTER_DOCUMENT_ALLOWANCE') ?: 100,
        ],
        'growth' => [
            'monthly' => env('DOCUFLOW_GROWTH_MONTHLY_PRICE') ?: 300000,
            'setup' => env('DOCUFLOW_GROWTH_SETUP_FEE') ?: 500000,
            'allowance' => env('DOCUFLOW_GROWTH_DOCUMENT_ALLOWANCE') ?: 300,
        ],
        'professional' => [
            'monthly' => env('DOCUFLOW_PROFESSIONAL_MONTHLY_PRICE') ?: 500000,
            'setup' => env('DOCUFLOW_PROFESSIONAL_SETUP_FEE') ?: 750000,
            'allowance' => env('DOCUFLOW_PROFESSIONAL_DOCUMENT_ALLOWANCE') ?: 750,
        ],
        'terms' => [
            'overage' => env('DOCUFLOW_OVERAGE_POLICY') ?: 'Additional documents above the monthly allowance are charged at UGX 1,000 per document. Customers are notified before recurring overage charges are applied.',
            'cancellation' => env('DOCUFLOW_CANCELLATION_POLICY') ?: 'Month-to-month subscription. No long-term contract is required. Customers may cancel before the next billing cycle. Setup fees are one-time and non-refundable once implementation and configuration work has started.',
        ],
    ],
];
