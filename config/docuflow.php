<?php

return [
    'contact' => [
        'email' => env('DOCUFLOW_CONTACT_EMAIL'),
        'phone' => env('DOCUFLOW_PHONE'),
        'whatsapp' => env('DOCUFLOW_WHATSAPP_NUMBER'),
    ],

    'leads' => [
        'email' => env('DOCUFLOW_LEADS_EMAIL'),
    ],

    'pricing' => [
        'starter' => [
            'monthly' => 150000,
            'setup' => 500000,
            'allowance' => env('DOCUFLOW_STARTER_DOCUMENT_ALLOWANCE'),
        ],
        'growth' => [
            'monthly' => env('DOCUFLOW_GROWTH_MONTHLY_PRICE'),
            'setup' => env('DOCUFLOW_GROWTH_SETUP_FEE'),
            'allowance' => env('DOCUFLOW_GROWTH_DOCUMENT_ALLOWANCE'),
        ],
        'professional' => [
            'monthly' => env('DOCUFLOW_PROFESSIONAL_MONTHLY_PRICE'),
            'setup' => env('DOCUFLOW_PROFESSIONAL_SETUP_FEE'),
            'allowance' => env('DOCUFLOW_PROFESSIONAL_DOCUMENT_ALLOWANCE'),
        ],
    ],
];
