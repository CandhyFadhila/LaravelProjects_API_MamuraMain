<?php

return [
  'enabled'           => (bool) env('RECAPTCHA_ENABLED', true),
  'site_key'          => env('RECAPTCHA_SITE_KEY'),
  'secret'            => env('RECAPTCHA_SECRET'),
  'min_score'         => (float) env('RECAPTCHA_MIN_SCORE', 0.6),
  'allowed_hostnames' => collect(explode(',', env('RECAPTCHA_ALLOWED_HOSTNAMES', '')))
    ->map(fn($h) => trim(strtolower($h)))
    ->map(fn($h) => preg_replace('#^https?://#', '', $h)) // buang scheme
    ->map(fn($h) => preg_replace('#:\d+$#', '', $h))      // buang port (localhost:5174 -> localhost)
    ->filter()
    ->values()
    ->all(),
];
