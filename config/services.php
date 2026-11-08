<?php

return [
    'openai' => ['key' => env('AI_API_KEY'), 'base_url' => env('AI_BASE_URL', 'https://api.openai.com/v1'), 'model' => env('AI_MODEL', 'gpt-4o-mini')],
    'firebase' => ['project_id' => env('FIREBASE_PROJECT_ID')],
];
