<?php

return [
    /**
     * Url the voice:fetch-key command will call to fetch the public key.
     */
    'ima_uri'                    => env('IAM_URI'),

    'iam_realm'                  => env('IAM_REALM'),

    'iam_key_url'                => env('IAM_URI') . '/auth/realms/' . env('IAM_REALM'),
    /**
     * Under what key in the response array can the public key be found.
     */
    'public_key_array_location'  => 'public_key',

    /**
     * Location where to save the public key.
     */
    'public_key'                 => env('JWT_PUBLIC_KEY', '/var/www/html/config/public.pem'),

    /**
     * User that will be injected into the Laravel auth middleware.
     */
    'user'                       => '\Voice\Auth\App\TokenUser',

    /**
     * If set to false, the verifier will not throw an exception and will load the user entity
     * into the auth regardless of validity.
     *
     * A new array key (voice_sys_validated) is inserted into the claims passed to the setFromClaims.
     * The value is a boolean that marks if the user is valid or not.
     */
    'throw_exception_on_invalid' => false,

    /**
     * If set to false, token expiration will not be checked.
     */
    'verify_expiration'          => true,

    /**
     * The key set here will be the one the decoder will look for in the claims array and set as the identifier.
     *
     * By default, user_id is set. This means that when the user object is built it will have a property
     * $userObject->user_id. However it is recommended to use $userObject->getId() that will return the
     * same thing or null if the property could not be found in the claims.
     */
    'user_identifier'            => 'user_id',

    /**
     * TODO: wrong readme, getId()?
     * The key set here will be the one the decoder will look for in the claims array and set as the identifier.
     *
     * By default, user_id is set. This means that when the user object is built it will have a property
     * $userObject->user_id. However it is recommended to user $userObject->getId() that will return the
     * same thing or null if the property could not be found in the claims.
     *
     * NOTE: If this key is found the TokenUser will be marked as a service user
     */
    'client_identifier'          => 'clientId',

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
    'claim_map'                  => [],

    /**
     * For dev purposes. Setting to true will ignore authentication completely.
     */
    'override_authentication'    => env('OVERRIDE_AUTHENTICATION', false) === true,

    'client_id' => env('CLIENT_ID'),

    'client_secret' => env('CLIENT_SECRET'),
];
