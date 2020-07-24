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
    'user' => '\Voice\Auth\App\User',
    /*
     * if set to false the verifier will not throw an exception and will load the user entity into the auth regardles
     * of validity
     * A new Array key (voice_sys_validated) is inserted into the claims passed to the setFromClaims. The value is a
     * boolen that marks if the user is valid or not
     */
    'throw_exception_on_invalid' => false,
    /**
     * if set to false token expiration will not be checked
     */
    'verify_expiration' => true
];
