<div class="min-h-screen flex flex-col items-center justify-center px-6 py-12">
    <!-- Logo -->
    <div
        class="w-24 h-24 md:w-28 md:h-28 bg-white/20 backdrop-blur-xl rounded-3xl flex items-center justify-center shadow-2xl mb-8 animate-pulse">
        <x-icon name="messages" class="w-12 h-12 md:w-14 md:h-14 text-white" />
    </div>

    <!-- App Name -->
    <h1 class="text-4xl md:text-5xl font-extrabold text-white text-center mb-3 tracking-tight">
        GroupChat
    </h1>

    <!-- Tagline -->
    <p class="text-lg md:text-xl text-white/80 text-center max-w-xs md:max-w-sm mb-12">
        Stay connected with your school community
    </p>

    <!-- OTP Card -->
    <div class="w-full max-w-sm bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-8" x-data="{ 
            countdown: @entangle('remainingCooldown'), 
            canResend: false,
            timer: null,
            init() {
                this.startTimer();
                $watch('countdown', value => {
                    if (value > 0) this.startTimer();
                });
                Livewire.on('otp-resent', ({ cooldown }) => {
                    this.countdown = cooldown;
                    this.startTimer();
                });
            },
            startTimer() {
                if (this.timer) clearInterval(this.timer);
                
                if (this.countdown > 0) {
                    this.canResend = false;
                    this.timer = setInterval(() => {
                        if (this.countdown > 0) {
                            this.countdown--;
                        } else {
                            this.canResend = true;
                            clearInterval(this.timer);
                        }
                    }, 1000);
                } else {
                    this.canResend = true;
                }
            }
        }">
        <p class="text-gray-600 text-center dark:text-gray-400 mb-6">
            Enter the 6-digit code sent to<br>
            <span class="font-medium text-gray-900 dark:text-white">{{ $phone }}</span>
        </p>

        @if(session('success'))
            <div
                class="mb-6 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('dev_otp'))
            <div
                class="mb-6 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-400 px-4 py-3 rounded-xl text-sm">
                <strong>Dev Mode OTP:</strong> {{ session('dev_otp') }}
            </div>
        @endif

        <form wire:submit="verify" class="space-y-6">
            <div>
                <input type="text" wire:model="otp" id="otp" required autofocus maxlength="6" pattern="[0-9]{6}"
                    class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-700 border-0 rounded-xl text-center text-3xl font-bold tracking-widest text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 @error('otp') ring-2 ring-red-500 @enderror"
                    placeholder="000000">
                @error('otp')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 text-center">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/30 transition-all flex justify-center items-center"
                wire:loading.attr="disabled" wire:target="verify">
                <span wire:loading.remove wire:target="verify">Verify & Login</span>
                <span wire:loading.class.remove="hidden" wire:loading.class="flex" wire:target="verify"
                    class="hidden items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Verifying...
                </span>
            </button>
        </form>

        <!-- Resend OTP -->
        <div class="mt-6 text-center">
            <p x-show="!canResend" class="text-sm text-gray-500 dark:text-gray-400">
                Resend code in <span class="font-medium text-indigo-600"
                    x-text="Math.floor(countdown / 60) + ':' + String(countdown % 60).padStart(2, '0')"></span>
            </p>
            <div x-show="canResend">
                <button wire:click="resend" wire:loading.attr="disabled"
                    class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                    Resend verification code
                </button>
            </div>
        </div>

        <!-- Change Phone -->
        <div class="mt-4 text-center">
            <a href="{{ route('login') }}"
                class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                Use a different phone number
            </a>
        </div>
    </div>
</div>