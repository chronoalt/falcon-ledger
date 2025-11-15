<?php

namespace App\Providers;

use App\Models\Asset;
use App\Models\Finding;
use App\Models\Project;
use App\Models\Target;
use App\Models\User;
use App\Policies\AssetPolicy;
use App\Policies\FindingPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\TargetPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            // Manually check for the admin role to bypass potential issues with hasRole()
            if ($user->roles->pluck('name')->contains('admin')) {
                return true;
            }
            return null;
        });

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(Asset::class, AssetPolicy::class);
        Gate::policy(Target::class, TargetPolicy::class);
        Gate::policy(Finding::class, FindingPolicy::class);
    }
}
