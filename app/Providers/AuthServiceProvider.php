<?php

namespace App\Providers;

 use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('access-student-info', function ($user, $student) {
            return $user->hasRole('school_admin') && $user->school_id === $student->school_id;
        });

        Gate::define('access-parent-info', function ($user, $parent) {
            return $user->hasRole('school_admin') && $user->school_id === $parent->school_id;
        });

        Gate::define('access-own-interviews', function ($user, $interview) {
            return $user->id === $interview->interviewer_id;
        });

        Gate::define('access-school-info', function ($user, $school) {
            return $user->hasRole('school_admin') && $user->school_id === $school->id;
        });

        Gate::define('access-SuperAdmin-and-SchoolAdmin', function ($user) {
            return $user->hasAnyRole(['SuperAdmin', 'SchoolAdmin']);
        });
    }
}
