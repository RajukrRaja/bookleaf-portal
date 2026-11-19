<?php

return [
    'bookleaf_ai' => [
        'url' => env('AI_CHAT_API_URL', 'https://vaionex19.com/api/chat'),
        'key' => env('AI_CHAT_API_KEY'),
        'timeout' => env('AI_CHAT_TIMEOUT', 60),
        'connect_timeout' => env('AI_CHAT_CONNECT_TIMEOUT', 15)
    ]
];
