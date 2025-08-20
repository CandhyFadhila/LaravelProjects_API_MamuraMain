<?php

return [
  'enabled'           => (bool) env('RECAPTCHA_ENABLED', true),
  'site_key'          => env('RECAPTCHA_SITE_KEY'),
  'secret'            => env('RECAPTCHA_SECRET'),
  'min_score'         => (float) env('RECAPTCHA_MIN_SCORE', 0.6),
  'allowed_hostnames' => array_values(array_filter(array_map('trim', explode(',', env('RECAPTCHA_ALLOWED_HOSTNAMES', ''))))),
];
