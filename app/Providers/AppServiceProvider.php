<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Promo;
use App\Models\RentalCompany;
use App\Models\Review;
use App\Models\UserNotification;
use App\Models\User;
use App\Models\Vehicle;
use App\Policies\BookingPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\PromoPolicy;
use App\Policies\RentalCompanyPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\VehiclePolicy;
use App\Policies\UserPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Gate::policy(Vehicle::class, VehiclePolicy::class);
        Gate::policy(Booking::class, BookingPolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Gate::policy(Promo::class, PromoPolicy::class);
        Gate::policy(Review::class, ReviewPolicy::class);
        Gate::policy(RentalCompany::class, RentalCompanyPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        view()->composer('*', function ($view): void {
            $user = auth()->user();

            if (!$user) {
                $view->with('layoutUnreadNotificationsCount', 0);
                $view->with('layoutRecentNotifications', collect());

                return;
            }

            $recentNotifications = UserNotification::query()
                ->where('user_id', (int) $user->id)
                ->latest('id')
                ->take(5)
                ->get();

            $view->with('layoutUnreadNotificationsCount', $recentNotifications->whereNull('read_at')->count());
            $view->with('layoutRecentNotifications', $recentNotifications);
        });
    }
}
