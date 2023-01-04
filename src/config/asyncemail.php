<?php

return [
    'mailjet' => [
        'key' => env('MAILJET_KEY', ''),
        'secret' => env('MAILJET_SECRET', ''),
        'senderEmail' => env('MAILJET_SENDER_EMAIL', ''),
    ],
    'sendgrid' => [
        'apiKey' => env('SENDGRID_API_KEY', ''),
        'senderEmail' => env('SENDGRID_SENDER_EMAIL', ''),
    ],
];
