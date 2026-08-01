<?php

use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\WhatsAppSettingController;
use App\Http\Controllers\Admin\WhatsAppLogController;
use App\Http\Controllers\Admin\LandingPageSettingsController;
use App\Http\Controllers\Admin\PanitiaController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Panitia\PasswordChangeController;
use App\Http\Controllers\RegistrationController;
use App\Http\Middleware\ThrottleRegistrationSubmission;
use App\Http\Middleware\EnsureSuperadmin;
use App\Http\Middleware\EnsurePanitia;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('landing');
Route::post('/registrations', [RegistrationController::class, 'store'])->middleware(ThrottleRegistrationSubmission::class)->name('registrations.store');
Route::get('/registration/success', [RegistrationController::class, 'success'])->name('registration.success');

Route::redirect('/login', '/admin/login');
Route::redirect('/admin/dashboard', '/dashboard');
Route::redirect('/admin', '/dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    
    // Superadmin routes
    Route::middleware(EnsureSuperadmin::class)->group(function () {
        Route::get('/dashboard/participants', ParticipantController::class)->name('dashboard.participants');
        Route::post('/dashboard/participants/{registration}/resend-whatsapp', [ParticipantController::class, 'resendWhatsApp'])->name('dashboard.participants.resend-whatsapp');
        Route::post('/dashboard/participants/resend-failed-whatsapp', [ParticipantController::class, 'resendFailedWhatsApp'])->name('dashboard.participants.resend-failed-whatsapp');
        Route::get('/dashboard/whatsapp-settings', [WhatsAppSettingController::class, 'index'])->name('dashboard.whatsapp-settings');
        Route::post('/dashboard/whatsapp-settings', [WhatsAppSettingController::class, 'update'])->name('dashboard.whatsapp-settings.update');
        Route::post('/dashboard/whatsapp-settings/test', [WhatsAppSettingController::class, 'testConnection'])->name('dashboard.whatsapp-settings.test');
        Route::get('/dashboard/whatsapp-logs', WhatsAppLogController::class)->name('dashboard.whatsapp-logs');
        Route::get('/dashboard/landing-settings', [LandingPageSettingsController::class, 'index'])->name('dashboard.landing-settings');
        Route::post('/dashboard/landing-settings', [LandingPageSettingsController::class, 'update'])->name('dashboard.landing-settings.update');
        
        // Panitia Management
        Route::post('/admin/panitia/{panitia}/activate', [PanitiaController::class, 'activate'])->name('admin.panitia.activate');
        Route::post('/admin/panitia/{panitia}/reset-password', [PanitiaController::class, 'resetPassword'])->name('admin.panitia.reset-password');
        Route::resource('admin/panitia', PanitiaController::class, [
            'names' => [
                'index' => 'admin.panitia.index',
                'create' => 'admin.panitia.create',
                'store' => 'admin.panitia.store',
                'edit' => 'admin.panitia.edit',
                'update' => 'admin.panitia.update',
                'destroy' => 'admin.panitia.destroy',
            ]
        ]);
        
        // Export
        Route::get('/admin/export/excel', [ExportController::class, 'exportExcel'])->name('admin.export.excel');
        Route::get('/admin/export/pdf', [ExportController::class, 'exportPdf'])->name('admin.export.pdf');
    });
    
    // Panitia routes
    Route::middleware(EnsurePanitia::class)->group(function () {
        Route::get('/panitia/password/change', [PasswordChangeController::class, 'show'])->name('panitia.password.change');
        Route::post('/panitia/password/change', [PasswordChangeController::class, 'update'])->name('panitia.password.change.update');
        // Data Anak for Pengurus
        Route::get('/panitia/participants', ParticipantController::class)->name('panitia.participants');
    });
});

require __DIR__.'/auth.php';
