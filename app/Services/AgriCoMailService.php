<?php

namespace App\Services;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AgriCoMailService
{
    /**
     * Send an email using Resend HTTP API first (for reliable cloud delivery on Railway),
     * falling back to standard Laravel Mail (Gmail SMTP).
     *
     * @param string $to
     * @param string $subject
     * @param string $view
     * @param array $viewData
     * @param Mailable|null $fallbackMailable
     * @return bool
     */
    public static function send(string $to, string $subject, string $view, array $viewData = [], ?Mailable $fallbackMailable = null): bool
    {
        $to = strtolower(trim($to));
        if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            Log::warning("AgriCoMailService: Invalid recipient email: '{$to}'");
            return false;
        }

        $sent = false;
        $sendError = null;

        // 1. Try Resend HTTP API
        $fromEmail = (string) config('services.resend.from', 'AgriCo Support <onboarding@resend.dev>');
        if (str_starts_with($fromEmail, 'RESEND_FROM=')) {
            $fromEmail = substr($fromEmail, strlen('RESEND_FROM='));
        }
        $fromEmail = trim($fromEmail, " \t\n\r\0\x0B\"'") ?: 'AgriCo Support <onboarding@resend.dev>';

        $resendKey = config('services.resend.key');
        if (is_string($resendKey)) {
            $resendKey = trim($resendKey);
            if (str_starts_with($resendKey, 'RESEND_API_KEY=')) {
                $resendKey = substr($resendKey, strlen('RESEND_API_KEY='));
            }
            $resendKey = trim($resendKey, " \t\n\r\0\x0B\"'");
        }

        if (class_exists(\Resend::class) && !empty($resendKey)) {
            try {
                $html = (string) view($view, $viewData)->render();
                $resend = \Resend::client($resendKey);
                $resend->emails->send([
                    'from'    => $fromEmail,
                    'to'      => [$to],
                    'subject' => $subject,
                    'html'    => $html,
                ]);
                $sent = true;
                Log::info("AgriCoMailService: Email '{$subject}' sent via Resend HTTP API to {$to}");
            } catch (\Throwable $re) {
                $sendError = $re->getMessage();
                Log::warning("AgriCoMailService: Resend HTTP API failed for {$to}: {$sendError}");
            }
        }

        // 2. Fallback to standard Laravel Mail (Gmail SMTP)
        if (!$sent) {
            try {
                if ($fallbackMailable) {
                    Mail::to($to)->send($fallbackMailable);
                } else {
                    Mail::send($view, $viewData, function ($message) use ($to, $subject) {
                        $message->to($to)->subject($subject);
                    });
                }
                $sent = true;
                Log::info("AgriCoMailService: Email '{$subject}' sent via Laravel Mail driver to {$to}");
            } catch (\Throwable $e) {
                $sendError = $e->getMessage();
                Log::error("AgriCoMailService: Mail driver failed for {$to}: {$sendError}");
            }
        }

        return $sent;
    }
}
