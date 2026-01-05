<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected ?Client $client = null;
    protected ?string $whatsappFrom = null;
    protected ?string $smsFrom = null;

    public function __construct()
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $this->whatsappFrom = config('services.twilio.whatsapp_from');
        $this->smsFrom = config('services.twilio.sms_from');

        if ($sid && $token) {
            $this->client = new Client($sid, $token);
        }
    }

    /**
     * Send OTP via WhatsApp
     */
    public function sendWhatsAppOtp(string $phone, string $otp): void
    {
        if (!$this->client) {
            throw new \Exception('Twilio credentials not configured');
        }

        // Format phone number for WhatsApp
        $to = 'whatsapp:' . $this->formatPhone($phone);
        $from = 'whatsapp:' . $this->whatsappFrom;

        $this->client->messages->create($to, [
            'from' => $from,
            'body' => "Your verification code is: {$otp}\n\nThis code expires in 10 minutes. Do not share this code with anyone.",
        ]);
    }

    /**
     * Send OTP via SMS
     */
    public function sendSmsOtp(string $phone, string $otp): void
    {
        if (!$this->client) {
            throw new \Exception('Twilio credentials not configured');
        }

        if (!$this->smsFrom) {
            throw new \Exception('Twilio SMS From number not configured');
        }

        $this->client->messages->create($this->formatPhone($phone), [
            'from' => $this->smsFrom,
            'body' => "Your verification code is: {$otp}. Exp: 10m. Don't share.",
        ]);
    }

    /**
     * Format phone number to E.164 format
     */
    protected function formatPhone(string $phone): string
    {
        // Remove any non-numeric characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // If doesn't start with +, assume it needs one
        if (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }

        return $phone;
    }
}
