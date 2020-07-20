<?php


namespace Voice\Auth;


use Voice\Auth\App\Decoder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AuthUserServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/voice-auth.php', 'voice-auth');

        Auth::provider('jwt_provider', function($app, array $config) {
            return new TokenUserProvider(
                $app->make(config('voice-auth.user')),
                new Decoder(
                    config('voice-auth.public_key'),
                    $app->make(config('voice-auth.user'))
                )
            );
        });
    }

}
