<?php

use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\BlogPostController as AdminBlogPostController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DestinationController as AdminDestinationController;
use App\Http\Controllers\Admin\GalleryItemController as AdminGalleryItemController;
use App\Http\Controllers\Admin\InquiryController as AdminInquiryController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Admin\TourCategoryController as AdminTourCategoryController;
use App\Http\Controllers\Admin\TourController as AdminTourController;
use App\Http\Controllers\Admin\TourGuideController as AdminTourGuideController;
use App\Http\Controllers\Admin\TourTypeController as AdminTourTypeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\InquiryController as CustomerInquiryController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\TourController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tours', [TourController::class, 'index'])->name('tours.index');
Route::get('/tours/{tour:slug}', [TourController::class, 'show'])->name('tours.show');
Route::post('/tours/{tour:slug}/book', [BookingController::class, 'store'])->name('tours.book');
Route::get('/destinations', [DestinationController::class, 'index'])->name('destinations.index');
Route::get('/destinations/{destination:slug}', [DestinationController::class, 'show'])->name('destinations.show');
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{blogPost:slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/terms', [LegalController::class, 'terms'])->name('terms');
Route::get('/privacy', [LegalController::class, 'privacy'])->name('privacy');
Route::get('/terms-and-conditions', fn () => redirect()->route('terms', [], 301));
Route::get('/privacy-policy', fn () => redirect()->route('privacy', [], 301));

Route::get('/admin/login', function () {
    return redirect()->route('login');
})->name('admin.login');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('dashboard')
    ->name('customer.')
    ->middleware(['auth', 'customer'])
    ->group(function (): void {
        Route::get('/', [CustomerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/bookings', [CustomerBookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings/{booking}/cancel', [CustomerBookingController::class, 'cancel'])->name('bookings.cancel');
        Route::get('/inquiries', [CustomerInquiryController::class, 'index'])->name('inquiries.index');
        Route::get('/profile', [CustomerProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [CustomerProfileController::class, 'update'])->name('profile.update');
    });

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function (): void {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/settings', [SiteSettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SiteSettingController::class, 'update'])->name('settings.update');

        Route::resource('categories', AdminTourCategoryController::class)->except(['show']);
        Route::resource('tour-types', AdminTourTypeController::class)->except(['show']);
        Route::resource('activities', AdminActivityController::class)->except(['show']);
        Route::resource('destinations', AdminDestinationController::class)->except(['show']);
        Route::resource('tours', AdminTourController::class)->except(['show']);
        Route::resource('gallery', AdminGalleryItemController::class)->except(['show']);
        Route::resource('guides', AdminTourGuideController::class)->except(['show']);
        Route::resource('testimonials', AdminTestimonialController::class)->except(['show']);
        Route::resource('blog-posts', AdminBlogPostController::class)->except(['show']);
        Route::resource('bookings', AdminBookingController::class)->only(['index', 'show', 'update', 'destroy']);
        Route::resource('inquiries', AdminInquiryController::class)->only(['index', 'show', 'update', 'destroy']);
    });
