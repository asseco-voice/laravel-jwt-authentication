<?php


namespace Voice\Auth;


use Voice\Auth\App\Console\Commands\FetchPublicKey;
use Voice\Auth\App\Decoder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AuthUserServiceProvider extends ServiceProvider
{
    public function boot()
    {

        $this->publishes([__DIR__ . '/config/asseco-voice.php' => config_path('asseco-voice.php'),]);

        $this->mergeConfigFrom(__DIR__ . '/config/asseco-voice.php', 'asseco-voice');
        $this->mergeConfigFrom(__DIR__ . '/config/guard.php', 'auth.guards');
        $this->mergeConfigFrom(__DIR__ . '/config/provider.php', 'auth.providers');

        Auth::provider('jwt_provider', function($app, array $config) {
            return new TokenUserProvider(
                $app->make(config('asseco-voice.authentication.user')),
                new Decoder(
                    config('asseco-voice.authentication.public_key'),
                    $app->make(config('asseco-voice.authentication.user'))
                )
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
