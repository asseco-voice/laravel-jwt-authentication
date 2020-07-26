### **Laravel Authentication**

This package enables backend authentication via JWT-token

#### **Installation**

```
composer require asseco-voice/authentication
```

In order to gain access to additional configuration run 
```
php artisan vendor:publish
```

This will publish the asseco-voice.php configuration file into your config directory

#####Quick usage

Minimal requirement to make the package work is to set the ```JWT_PUBLIC_KEY``` in your .env file.
This is the location of the public with which the package will verify the signature of the token.

The package works out-of-the-box. Once required the application will have a new guard registered
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

#### **Fetch key command**

Package comes with a convenience command. The only requirement is the env value ```IAM_KEY_URL```

```
    php artisan asseco-voice:fetch-key
```

The method is made to hit the configured end-point and extract the public key from the response.
By default the command will look for the key 'public_key' in the response, however this can 
be configured through the config file.


