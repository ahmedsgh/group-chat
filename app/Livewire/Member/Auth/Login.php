<?php

namespace App\Livewire\Member\Auth;

use App\Models\Member;
use App\Models\MemberOtp;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.guest')]
#[Title('Member Login')]
class Login extends Component
{
    public $phone = '';

    public function sendOtp()
    {
        $validated = $this->validate([
            'phone' => 'required|string|max:20',
        ]);

        // Check if member exists
        $member = Member::where('phone', $validated['phone'])->first();

        if (!$member) {
            $this->addError('phone', 'This phone number is not registered.');
            return;
        }

        // Check if can request new OTP (rate limiting)
        if (!MemberOtp::canRequestNewOtp($validated['phone'])) {
            $remainingSeconds = MemberOtp::getRemainingCooldown($validated['phone']);
            $this->addError('phone', "Please wait {$remainingSeconds} seconds before requesting a new OTP.");
            return;
        }

        // Generate OTP
        $otpCode = MemberOtp::generateCode();

        // Store OTP
        MemberOtp::create([
            'phone' => $validated['phone'],
            'otp' => $otpCode,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send OTP via Twilio
        try {
            $twilioService = app(TwilioService::class);

            // Try WhatsApp first if configured
            if (config('services.twilio.whatsapp_from')) {
                try {
                    $twilioService->sendWhatsAppOtp($validated['phone'], $otpCode);
                } catch (\Exception $e) {
                    // Fallback to SMS if WhatsApp fails and SMS is configured
                    if (config('services.twilio.sms_from')) {
                        $twilioService->sendSmsOtp($validated['phone'], $otpCode);
                    } else {
                        throw $e;
                    }
                }
            } elseif (config('services.twilio.sms_from')) {
                // Only SMS configured
                $twilioService->sendSmsOtp($validated['phone'], $otpCode);
            } else {
                throw new \Exception('No Twilio sender configured (WhatsApp or SMS)');
            }

        } catch (\Exception $e) {
            // Log error but continue (for development without Twilio)
            \Log::warning('Failed to send OTP via Twilio: ' . $e->getMessage());

            // In development, flash the OTP to session (remove in production)
            if (app()->environment('local')) {
                Session::flash('dev_otp', $otpCode);
            }
        }

        // Store phone in session for OTP verification
        Session::put('otp_phone', $validated['phone']);

        return redirect()->route('otp')
            ->with('success', 'OTP sent to your WhatsApp.');
    }

    public function render()
    {
        return view('livewire.member.auth.login');
    }
}
