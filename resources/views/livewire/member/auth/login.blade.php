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

    <!-- Login Card -->
    <div class="w-full max-w-sm bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-8">
        <div class="text-center">
            <h2 class="text-gray-900 text-lg dark:text-white mb-2">Enter your phone number to connect</h2>
            <p class="text-gray-600 text-sm dark:text-gray-400 mb-6">We will send you one time password (OTP)</p>
        </div>

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

        <form wire:submit="sendOtp" class="space-y-6">
            <div>
                <div class="relative">
                    <x-icon name="phone" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" />
                    <input type="tel" wire:model="phone" id="phone" required autofocus
                        class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-gray-700 border-0 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 @error('phone') ring-2 ring-red-500 @enderror"
                        placeholder="+1234567890">
                </div>
                @error('phone')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/30 transition-all flex justify-center items-center"
                wire:loading.attr="disabled" wire:target="sendOtp">
                <span wire:loading.remove wire:target="sendOtp">Send Verification Code</span>
                <span wire:loading.class.remove="hidden" wire:loading.class="flex" wire:target="sendOtp"
                    class="hidden items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Sending...
                </span>
            </button>
        </form>


    </div>

    <!-- Back to Home -->
    <div class="text-center mt-6">
        <a href="{{ route('home') }}" class="text-white/80 hover:text-white text-sm inline-flex items-center">
            <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
            Back to Home
        </a>
    </div>
</div>