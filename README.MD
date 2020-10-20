<p align="center"><a href="https://see.asseco.com" target="_blank"><img src="https://github.com/asseco-voice/art/blob/main/evil_logo.png" width="500"></a></p>

# Laravel JWT Authentication

This package enables backend authentication via JWT-token

## Installation

Install the package through composer. It is automatically registered
as a Laravel service provider, so no additional actions are required to register the package.

```
composer require asseco-voice/laravel-jwt-authentication
```

In order to gain access to additional configuration run 
```
php artisan vendor:publish
```

This will publish the asseco-authentication.php configuration file into your config directory

## Quick usage

A minimal requirement to make the package work is to set the ```JWT_PUBLIC_KEY``` in your .env file.
This is the location of the public key with which the package will verify the signature of the token.

The package works out-of-the-box. 
Once installed the application will have a new guard registered.
```
'jwt-api' => [
   'driver' => 'token',
   'provider' => 'token_provider',
],
```

Activating it on your desired route follows the standard laravel implementation.

```Route::apiResource('YourResource', 'YourController')->middleware('auth:jwt-api');```

This will force the framework to use the following configuration to resolve the user from a JWT-token and 
injecting it as the authenticated user. 
```
'token_provider' => [
    'driver' => 'jwt_provider'
]
```

## Fetch key command

Package comes with a convenience command. The only requirement is the env value ```IAM_KEY_URL```.

```
php artisan voice:fetch-key
```

The method is made to hit the configured endpoint and extract the public key from the response.
By default, the command will look for the key `public_key` in the response, however this can 
be configured through a config file.

## Additional

For dev purposes, you can disable authentication completely by adding this to your ``.env`` file:

    OVERRIDE_AUTHENTICATION=true
