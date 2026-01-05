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
#[Title('Verify OTP')]
class VerifyOtp extends Component
{
    public $otp = '';
    public $phone;
    public $remainingCooldown = 0;

    public function mount()
    {
        $this->phone = Session::get('otp_phone');

        if (!$this->phone) {
            return redirect()->route('login');
        }

        $this->remainingCooldown = MemberOtp::getRemainingCooldown($this->phone);
    }

    public function verify()
    {
        $this->validate([
            'otp' => 'required|string|size:6',
        ]);

        // Find valid OTP
        $otpRecord = MemberOtp::where('phone', $this->phone)
            ->where('otp', $this->otp)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otpRecord) {
            $this->addError('otp', 'Invalid or expired OTP. Please try again.');
            return;
        }

        // Mark OTP as used
        $otpRecord->markAsUsed();

        // Get member
        $member = Member::where('phone', $this->phone)->first();

        // Login using member guard
        auth()->guard('member')->login($member);
        Session::forget('otp_phone');

        // Update last seen
        $member->update(['last_seen_at' => now()]);

        return redirect()->route('messages')
            ->with('success', 'Welcome back, ' . $member->name . '!');
    }

    public function resend()
    {
        if (!MemberOtp::canRequestNewOtp($this->phone)) {
            $this->remainingCooldown = MemberOtp::getRemainingCooldown($this->phone);
            $this->addError('otp', "Please wait {$this->remainingCooldown} seconds before requesting a new OTP.");
            return;
        }

        // Generate OTP
        $otpCode = MemberOtp::generateCode();

        // Store OTP
        MemberOtp::create([
            'phone' => $this->phone,
            'otp' => $otpCode,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send OTP via Twilio
        try {
            $twilioService = app(TwilioService::class);

            // Try WhatsApp first if configured
            if (config('services.twilio.whatsapp_from')) {
                try {
                    $twilioService->sendWhatsAppOtp($this->phone, $otpCode);
                } catch (\Exception $e) {
                    // Fallback to SMS if WhatsApp fails and SMS is configured
                    if (config('services.twilio.sms_from')) {
                        $twilioService->sendSmsOtp($this->phone, $otpCode);
                    } else {
                        throw $e;
                    }
                }
            } elseif (config('services.twilio.sms_from')) {
                // Only SMS configured
                $twilioService->sendSmsOtp($this->phone, $otpCode);
            } else {
                throw new \Exception('No Twilio sender configured');
            }

            session()->flash('success', 'OTP resent successfully.');

        } catch (\Exception $e) {
            \Log::warning('Failed to resend OTP via Twilio: ' . $e->getMessage());

            if (app()->environment('local')) {
                Session::flash('dev_otp', $otpCode);
            }

            session()->flash('error', 'Failed to send OTP provided. Please try again.');
        }

        // Reset cooldown
        $this->remainingCooldown = MemberOtp::getRemainingCooldown($this->phone);
        // Force re-render to update alpine component
        $this->dispatch('otp-resent', cooldown: $this->remainingCooldown);
    }

    public function render()
    {
        return view('livewire.member.auth.verify-otp');
    }
}
