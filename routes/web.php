<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Livewire\Member\Auth\Login as MemberLogin;
use App\Livewire\Member\Auth\VerifyOtp as MemberVerifyOtp;
use App\Livewire\Member\Messages as MemberMessages;
use App\Livewire\Admin\Members\MemberIndex;
use App\Livewire\Admin\Members\MemberForm;
use App\Livewire\Admin\Members\MemberShow;
use App\Livewire\Admin\Groups\GroupIndex;
use App\Livewire\Admin\Groups\GroupForm;
use App\Livewire\Admin\Groups\GroupShow;
use App\Livewire\Admin\Messages\MessageCenter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['guest:web', 'guest:member'])->group(function () {
    Route::get('/', function () {
        return view('guest.home');
    })->name('home');
});

/*
|--------------------------------------------------------------------------
| Member Routes (Main - No Prefix)
|--------------------------------------------------------------------------
*/
// Auth (guest members)
Route::middleware(['guest:web', 'guest:member'])->group(function () {
    Route::get('/login', MemberLogin::class)->name('login');
    Route::get('/otp', MemberVerifyOtp::class)->name('otp');
});

// Authenticated member routes
Route::middleware('auth:member')->group(function () {
    Route::post('/logout', function () {
        Auth::guard('member')->logout();
        Session::forget('otp_phone');
        return redirect()->route('home')->with('success', 'You have been logged out.');
    })->name('member.logout');
    Route::get('/messages', MemberMessages::class)->name('messages');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Prefixed with /admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'verified'])->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Members CRUD (Livewire full-page components)
    Route::get('members', MemberIndex::class)->name('members.index');
    Route::get('members/create', MemberForm::class)->name('members.create');
    Route::get('members/{member}', MemberShow::class)->name('members.show');
    Route::get('members/{member}/edit', MemberForm::class)->name('members.edit');

    // Groups CRUD (Livewire full-page components)
    Route::get('groups', GroupIndex::class)->name('groups.index');
    Route::get('groups/create', GroupForm::class)->name('groups.create');
    Route::get('groups/{group}', GroupShow::class)->name('groups.show');
    Route::get('groups/{group}/edit', GroupForm::class)->name('groups.edit');

    // Messages (Livewire full-page component)
    Route::get('/messages', MessageCenter::class)->name('messages.index');
});

// Admin auth routes (from Breeze)
require __DIR__ . '/auth.php';
