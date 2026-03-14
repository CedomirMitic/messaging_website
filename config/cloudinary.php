<?php

return [
    'cloud_url' => env('CLOUDINARY_URL'),
    
    // Direktno mapiranje koje paket traži da ne bi pucao "Undefined key cloud"
    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key'    => env('CLOUDINARY_API_KEY'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),

    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),
    'upload_preset'    => env('CLOUDINARY_UPLOAD_PRESET'),
];