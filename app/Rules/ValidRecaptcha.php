<?php

namespace App\Rules;

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Services\RecaptchaVerifier;
use Illuminate\Support\Facades\Log;

class ValidRecaptcha implements ValidationRule
{
    public function __construct(
        protected string $expectedAction = 'carrier_apply'
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || trim($value) === '') {
            Log::channel('public_recaptcha')->warning('Rule: missing captcha token.', [
                'trace_id' => request()->header('X-Request-ID'),
                'attribute' => $attribute,
            ]);
            $fail('Token reCAPTCHA wajib diisi.');
            return;
        }

        $verifier = app(RecaptchaVerifier::class);
        $result   = $verifier->verify($value, $this->expectedAction, request()->ip());

        if (!($result['success'] ?? false)) {
            Log::channel('public_recaptcha')->notice('Rule: reCAPTCHA rejected by verifier.', [
                'trace_id' => $result['trace_id'] ?? null,
                'attribute' => $attribute,
                'reason'   => ($result['error-codes'][0] ?? 'unknown') . ($result['context_ok'] ?? false ? '' : ':context'),
                'score'    => $result['score'] ?? null,
                'action'   => $result['action'] ?? null,
                'hostname' => $result['hostname'] ?? null,
            ]);

            // Beri pesan spesifik agar FE tahu harus minta ulang challenge
            if (in_array('recaptcha-unavailable', $result['error-codes'] ?? [], true)) {
                $fail('Layanan verifikasi reCAPTCHA sedang tidak tersedia. Silakan coba lagi.');
                return;
            }

            // Action / hostname mismatch
            if (!($result['context_ok'] ?? false)) {
                $fail('Verifikasi reCAPTCHA tidak valid untuk konteks permintaan ini.');
                return;
            }

            // Skor rendah
            $score = number_format((float)($result['score'] ?? 0), 2);
            $fail("Verifikasi reCAPTCHA gagal (skor: {$score}). Silakan ulangi.");
        }
    }
}
