<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \App\Contracts\Repositories\UserRepositoryInterface::class,
            \App\Repositories\UserRepository::class,
        );

        $this->app->bind(
            \App\Contracts\Repositories\InvoiceRepositoryInterface::class,
            \App\Repositories\InvoiceRepository::class,
        );

        $this->app->bind(
            \App\Contracts\Repositories\TeamRepositoryInterface::class,
            \App\Repositories\TeamRepository::class,
        );

        $this->app->bind(
            \App\Contracts\Repositories\ContactRepositoryInterface::class,
            \App\Repositories\ContactRepository::class,
        );

        $this->app->bind(
            \App\Contracts\Repositories\InvitationRepositoryInterface::class,
            \App\Repositories\InvitationRepository::class,
        );

        $this->app->bind(
            \App\Contracts\Repositories\WebhookRepositoryInterface::class,
            \App\Repositories\WebhookRepository::class,
        );
    }
}
