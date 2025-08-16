<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicPolicyTicketController;

Route::get('/', function () {
    return redirect('/admin/login');
});

// Public Policy Ticket Routes
Route::prefix('ticket')->name('public.ticket.')->group(function () {
    Route::get('/', [PublicPolicyTicketController::class, 'showAccessForm'])->name('access-form');
    Route::post('/access', [PublicPolicyTicketController::class, 'showTicket'])->name('access');
    Route::get('/check/{ticket_number}', [PublicPolicyTicketController::class, 'showTicketByNumber'])->name('check');
    Route::post('/verify/{ticket_number}', [PublicPolicyTicketController::class, 'verifyAndShowTicket'])->name('verify');
    Route::get('/staff/{ticket_number}', [PublicPolicyTicketController::class, 'showStaffForm'])->name('staff-form');
    Route::get('/staff-verify/{ticket_number}', [PublicPolicyTicketController::class, 'redirectToStaffForm'])->name('staff-verify-redirect');
    Route::post('/staff-verify/{ticket_number}', [PublicPolicyTicketController::class, 'verifyStaffAccess'])->name('staff-verify');
    Route::post('/staff-update/{ticket_number}', [PublicPolicyTicketController::class, 'updateStaffInfo'])->name('staff-update');
    
    // API Routes
    Route::prefix('api')->name('api.')->group(function () {
        Route::post('/check-status', [PublicPolicyTicketController::class, 'checkStatus'])->name('check-status');
    });
});

// Staff URL Route (สำหรับ Filament form)
Route::get('/ticket/staff-verify/{ticket_number}/{access_code}', [PublicPolicyTicketController::class, 'staffVerifyWithCode'])->name('ticket.staff-verify');
