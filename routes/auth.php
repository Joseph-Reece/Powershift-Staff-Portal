<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Farmer\Auth\AuthenticatedSessionController as FarmerAuthenticatedSessionController;
use App\Http\Controllers\Farmer\Auth\PasswordResetLinkController as FarmerPasswordResetLinkController;
use App\Http\Controllers\Farmer\Auth\NewPasswordController as FarmerNewPasswordController;

use Illuminate\Support\Facades\Route;

// Route::get('/register', [RegisteredUserController::class, 'create'])
//                 ->middleware('guest')
//                 ->name('register');

// Route::post('/register', [RegisteredUserController::class, 'store'])
//                 ->middleware('guest');

Route::get('/login', [AuthenticatedSessionController::class, 'create'])
                ->middleware('guest')
                ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
                ->middleware('guest');

Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
                ->middleware('guest')
                ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
                ->middleware('guest')
                ->name('password.email');

Route::get('/reset-password/{staffNo}', [NewPasswordController::class, 'create'])
                ->middleware('guest')
                ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
                ->middleware('guest')
                ->name('password.update');

Route::get('/verify-email', [EmailVerificationPromptController::class, '__invoke'])
                ->middleware('auth')
                ->name('verification.notice');

Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
                ->middleware(['auth', 'signed', 'throttle:6,1'])
                ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware(['auth', 'throttle:6,1'])
                ->name('verification.send');

Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->middleware('auth')
                ->name('password.confirm');

Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])
                ->middleware('auth');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->middleware('auth')
                ->name('logout');

//custom
Route::get('/change-password', [NewPasswordController::class, 'changePassword'])
->middleware('guest')
->name('password.change');

Route::post('/change-password', [NewPasswordController::class, 'updatePassword'])
->middleware('guest')
->name('password.change');
//
Route::get('/updates', [AuthenticatedSessionController::class, 'updates'])
                ->middleware('guest')
                ->name('updates');
/**
 *
 * FARMER
 */
Route::group(['prefix' => 'farmer'], function(){
    Route::get('/login', [FarmerAuthenticatedSessionController::class, 'create'])
                    ->middleware('guest')
                    ->name('frm_login');

    Route::post('/login', [FarmerAuthenticatedSessionController::class, 'store'])
                    ->middleware('guest');

    Route::get('/forgot-password', [FarmerPasswordResetLinkController::class, 'create'])
                    ->middleware('guest')
                    ->name('frm_password.request');

    Route::post('/forgot-password', [FarmerPasswordResetLinkController::class, 'store'])
                    ->middleware('guest')
                    ->name('frm_password.email');

    Route::get('/reset-password/{farmerNo}', [FarmerNewPasswordController::class, 'create'])
                    ->middleware('guest')
                    ->name('frm_password.reset');

    Route::post('/reset-password', [FarmerNewPasswordController::class, 'store'])
                    ->middleware('guest')
                    ->name('frm_password.update');
    Route::get('/change-password', [FarmerNewPasswordController::class, 'changePassword'])
                    ->middleware('guest')
                    ->name('frm_password.change');
    Route::post('/change-password', [FarmerNewPasswordController::class, 'updatePassword'])
                    ->middleware('guest')
                    ->name('frm_password.change');
});
