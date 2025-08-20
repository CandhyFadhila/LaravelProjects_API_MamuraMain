<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RecaptchaVerifier
{
  public function verify(string $token, string $expectedAction, ?string $remoteIp = null): array
  {
    $traceId  = request()->header('X-Request-ID') ?: (string) Str::uuid();
    $remoteIp = $remoteIp ?: request()->ip();
    $tokenSig = substr(hash('sha256', $token), 0, 12); // fingerprint aman, bukan token asli
    $route    = request()->path();
    $host     = request()->getHost();
    $carrier  = request('carrier_id'); // form field

    $baseCtx = [
      'trace_id'       => $traceId,
      'expected_action' => $expectedAction,
      'remote_ip'      => $remoteIp,
      'request_host'   => $host,
      'route'          => $route,
      'carrier_id'     => $carrier,
      'token_sig'      => $tokenSig,
    ];

    if (!config('recaptcha.enabled')) {
      Log::channel('public_recaptcha')->info('reCAPTCHA bypassed (disabled in config).', $baseCtx);

      return [
        'success'   => true,
        'score'     => 1.0,
        'action'    => $expectedAction,
        'hostname'  => $host,
        'bypassed'  => true,
        'trace_id'  => $traceId,
      ];
    }

    try {
      $response = Http::asForm()
        ->timeout(3)
        ->retry(1, 150)
        ->post('https://www.google.com/recaptcha/api/siteverify', array_filter([
          'secret'   => config('recaptcha.secret'),
          'response' => $token,
          'remoteip' => $remoteIp,
        ]));
    } catch (\Throwable $e) {
      Log::channel('public_recaptcha')->error('reCAPTCHA http exception.', $baseCtx + [
        'reason'   => 'http-exception',
        'message'  => $e->getMessage(),
        'class'    => get_class($e),
      ]);

      return [
        'success'      => false,
        'error-codes'  => ['recaptcha-unavailable'],
        'trace_id'     => $traceId,
      ];
    }

    if (!$response->ok()) {
      Log::channel('public_recaptcha')->error('reCAPTCHA service unavailable.', $baseCtx + [
        'reason' => 'http-non-200',
        'status' => $response->status(),
        // body jangan di-log penuh; sering kosong/HTML
      ]);

      return [
        'success'      => false,
        'error-codes'  => ['recaptcha-unavailable'],
        'trace_id'     => $traceId,
      ];
    }

    $data = $response->json();

    // normalisasi
    $data['score']    = $data['score']    ?? 0.0;
    $data['action']   = $data['action']   ?? null;
    $data['hostname'] = $data['hostname'] ?? null;

    // validasi konteks
    $allowedHosts = config('recaptcha.allowed_hostnames', []);
    $hostOk   = empty($allowedHosts) || in_array($data['hostname'], $allowedHosts, true);
    $actionOk = ($data['action'] === $expectedAction);
    $scoreOk  = ($data['score'] >= config('recaptcha.min_score'));
    $apiOk    = (bool) ($data['success'] ?? false);

    $success  = $apiOk && $actionOk && $hostOk && $scoreOk;
    $data['context_ok'] = $hostOk && $actionOk;

    // tentukan reason untuk logging
    $reason = 'ok';
    if (!$apiOk) {
      $reason = 'api-failure';
    } elseif (!$hostOk) {
      $reason = 'hostname-mismatch';
    } elseif (!$actionOk) {
      $reason = 'action-mismatch';
    } elseif (!$scoreOk) {
      $reason = 'low-score';
    }

    $logCtx = $baseCtx + [
      'reason'   => $reason,
      'success'  => $success,
      'api_ok'   => $apiOk,
      'action'   => $data['action'],
      'hostname' => $data['hostname'],
      'score'    => $data['score'],
      'min_score' => config('recaptcha.min_score'),
      'allowed_hostnames' => $allowedHosts,
      'error_codes' => $data['error-codes'] ?? null,
    ];

    if ($success) {
      Log::channel('public_recaptcha')->info('reCAPTCHA verified (success).', $logCtx);
    } else {
      // low-score / mismatch → warning; api failure/unavailable → error
      if (in_array($reason, ['low-score', 'hostname-mismatch', 'action-mismatch'], true)) {
        Log::channel('public_recaptcha')->warning('reCAPTCHA verification failed.', $logCtx);
      } else {
        Log::channel('public_recaptcha')->error('reCAPTCHA verification failed (service/api).', $logCtx);
      }
    }

    return ['success' => $success, 'trace_id' => $traceId] + $data;
  }
}
