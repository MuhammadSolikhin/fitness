<?php

use App\Http\Controllers\admin\ScheduleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PelatihController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\admin\UserController as AdminUserController;
use App\Http\Controllers\admin\ClassController as AdminClassesController;
use App\Http\Controllers\UserController as RegulerUserController;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('users', AdminUserController::class);
    Route::resource('classes', AdminClassesController::class);
    Route::resource('schedules', ScheduleController::class);
    Route::get('/api/schedules', function (Request $request) {
        $date = $request->query('date');
        return Schedule::where('schedule_date', $date)->get(['start_time', 'end_time']);
    });
    Route::get('/admin/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/admin/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
    Route::post('/admin/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
    Route::get('/admin/payments/history', [PaymentController::class, 'history'])->name('payments.history');

});

Route::middleware(['auth', 'role:pelatih'])->group(function () {
    Route::get('/pelatih/dashboard', [PelatihController::class, 'dashboard'])->name('pelatih.dashboard');
    Route::get('/pelatih/kelas-saya', [PelatihController::class, 'myClasses'])->name('pelatih.kelas-saya');
    Route::get('/pelatih/classes/{class}', [PelatihController::class, 'show'])->name('pelatih.classes.show');
    Route::post('/pelatih/classes/{class}/add-user', [PelatihController::class, 'addUser'])->name('pelatih.classes.addUser')->middleware('auth', 'role:pelatih');
    Route::patch('/pelatih/classes/{class}/users/{user}/membership', [PelatihController::class, 'updateMembership'])
        ->name('pelatih.classes.updateMembership');
    Route::get('/pelatih/jadwal-mengajar', [PelatihController::class, 'jadwal'])->name('pelatih.jadwal');
    Route::get('/pelatih/jadwal-mengajar/events', [PelatihController::class, 'getEvents'])->name('pelatih.jadwal.events');
    Route::get('/pelatih/payments', [PelatihController::class, 'payments'])->name('pelatih.payments.index');

});

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', [RegulerUserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/user/classes', [RegulerUserController::class, 'availableClasses'])->name('user.classes.available');
    Route::get('/user/my-classes', [RegulerUserController::class, 'myClasses'])->name('user.classes.my');
    Route::get('/user/my-classes/{class}', [RegulerUserController::class, 'showClass'])->name('user.classes.show');
    Route::get('/user/membership', [MembershipController::class, 'index'])->name('user.membership');
    Route::post('/user/membership/register', [MembershipController::class, 'register'])->name('user.membership.register');
    Route::post('/user/class/register', [RegulerUserController::class, 'registerClass'])->name('user.class.register');
    Route::get('/user/payments/history', [RegulerUserController::class, 'history'])->name('user.payments.history');
    Route::post('/user/class/pay-per-session', [RegulerUserController::class, 'payPerSession'])->name('user.class.payPerSession');
    Route::post('/payment/{id}/cancel', [RegulerUserController::class, 'cancelPayment'])->name('user.payment.cancel');
});

require __DIR__ . '/auth.php';
