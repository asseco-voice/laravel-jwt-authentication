<?php

namespace Voice\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Voice\Auth\App\Console\Commands\FetchPublicKey;
use Voice\Auth\App\Decoder;

class AuthUserServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/asseco-authentication.php', 'asseco-authentication');
        $this->mergeConfigFrom(__DIR__ . '/config/guard.php', 'auth.guards');
        $this->mergeConfigFrom(__DIR__ . '/config/provider.php', 'auth.providers');
    }

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([__DIR__ . '/config/asseco-authentication.php' => config_path('asseco-authentication.php'),]);

        Auth::provider('jwt_provider', function ($app, array $config) {
            return new TokenUserProvider(
                $app->make(config('asseco-authentication.user')),
                new Decoder(
                    config('asseco-authentication.public_key'),
                    $app->make(config('asseco-authentication.user'))
                )
            );
        });

        $this->prependMiddleware();
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

    protected function prependMiddleware(): void
    {
        $override = Config::get('asseco-authentication.override_authentication');

        if (!$override) {
            $router = $this->app['router'];
            $router->prependMiddlewareToGroup('api', 'auth:jwt-api');
        }
    }
}
