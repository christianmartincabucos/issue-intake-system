<?php

namespace App\Providers;

use App\Repositories\Contracts\IssueRepositoryInterface;
use App\Repositories\EloquentIssueRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IssueRepositoryInterface::class, EloquentIssueRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
