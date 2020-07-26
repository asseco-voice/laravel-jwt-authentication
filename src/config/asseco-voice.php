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
    'authentication' => [
        /**
         * Url the voice-auth:key command will call to fetch the public key
         */
        'iam_key_url' => env('IAM_KEY_URL'),
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
        'user' => '\Voice\Auth\App\TokenUser',
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
        'verify_expiration' => true,
        /**
         * The key set here will be the one the decoder will look for in the claims array and set as the identifier.
         *
         * As default user_id is set. This means that when the user object is build it will have a property
         * $userObject->user_id. However it is recommended to user $userObject->getId() that will return the same thing or
         * null if the property could not be found in the claims
         */
        'user_identifier' => 'user_id',
        /**
         * Any additional claim values you wish to map should be set here as a key=>value pair where the key is the
         * name of attribute within the token. The same way you would use the Laravel array helper you can set the key
         * to search the claims.
         *
         * example:
         * 'claim_map' => [
         *     'group.subgroup' => 'someSubgroup'
         * ]
         * This will search for the value subgroup in the group array of the claims and set it as a property
         * someSubgroup in the user object
         */
        'claim_map' => [],
    ]
];
