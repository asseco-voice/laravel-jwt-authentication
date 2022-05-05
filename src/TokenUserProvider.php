<?php

namespace Asseco\Auth;

use Asseco\Auth\App\Exceptions\MissingApiToken;
use Asseco\Auth\App\Service\Decoder;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Arr;
use Throwable;

class TokenUserProvider implements UserProvider
{
    public function __construct(private Decoder $decoder)
    {
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function retrieveByCredentials(array $credentials)
    {
        $token = Arr::get($credentials, 'api_token');

        throw_if(!$token, new MissingApiToken());

        return $this->decoder->decodeToken($token)->getUser();
    }

    /**
     * @inheritDoc
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // TODO: implement correctly
        return true;
    }

    /**
     * @inheritDoc
     */
    public function retrieveById($identifier)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function retrieveByToken($identifier, $token)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        return false;
    }
}
