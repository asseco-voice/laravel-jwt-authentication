<?php

namespace Asseco\Auth;

use Asseco\Auth\App\Console\Commands\FetchPublicKey;
use Asseco\Auth\App\Service\Decoder;
use Asseco\Auth\App\Service\KeyFetcher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AuthUserServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/asseco-authentication.php', 'asseco-authentication');
        $this->mergeConfigFrom(__DIR__ . '/../config/guard.php', 'auth.guards');
        $this->mergeConfigFrom(__DIR__ . '/../config/provider.php', 'auth.providers');
    }

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([__DIR__ . '/../config/asseco-authentication.php' => config_path('asseco-authentication.php')]);

        $this->app->bind(Decoder::class, function ($app) {
            return new Decoder(
                config('asseco-authentication.public_key'),
                $app->make(config('asseco-authentication.user')),
                new KeyFetcher()
            );
        });

        Auth::provider('jwt_provider', function ($app) {
            return new TokenUserProvider(
                $app->make(Decoder::class)
            );
        });

        $this->registerCommands();
    }


    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                FetchPublicKey::class,
            ]);
        }
    }
}
