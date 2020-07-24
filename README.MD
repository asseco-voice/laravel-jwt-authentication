### **Laravel Authentication**

This package enables backend authentication via JWT-token

#### **Installation**

```composer require asseco-voice/authenticaion```

Quick usage

The package works out-of-the-box. Once required the application will have a new guard registered
```
        'jwt-api' => [
           'driver' => 'token',
           'provider' => 'token_provider',
        ],
```

Activating it on your desired route follows the standard laravel implementation.

```Route::apiResource('YourResource', 'YourController')->middleware('auth:jwt-api');```

This will force the framework to use the 
```php
'token_provider' => [
        'driver' => 'jwt_provider'
    ]
```
to resolve the user from a JWT-token and injecting it as the authenticated user. 
