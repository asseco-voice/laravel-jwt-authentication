<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Sean Tymon <tymon148@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    /**
     * Url the voice-auth:key command will call to fetch the public key
     */
    'iam_key_url' => env('KEYCLOAK_LOCATION') . '/auth/realms/' . env('APP_NAME') . '/',
    /**
     * Under what key in the response array can the public key be found
     */
    'public_key_array_location' => 'public_key',
    /**
     * Location where to save the public key
     */
    'public_key' => env("JWT_PUBLIC_KEY"),
    /**
     * User that will be injected into the Laravel auth middleware
     */
    'user' => '\Voice\Auth\App\User'
];
