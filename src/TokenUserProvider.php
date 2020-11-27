<?php

namespace Voice\Auth;

use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Arr;
use Voice\Auth\App\Decoder;
use Voice\Auth\App\Interfaces\TokenUserInterface;

class TokenUserProvider implements UserProvider
{
    /**
     * @var TokenUserInterface
     */
    private TokenUserInterface $userModel;
    /**
     * @var Decoder
     */
    private Decoder $decoder;
    /**
     * @var array
     */
    private array $config;

    public function __construct(
        TokenUserInterface $userModel,
        Decoder $decoder,
        array $config = []
    ) {
        $this->userModel = $userModel;
        $this->decoder = $decoder;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function retrieveByCredentials(array $credentials)
    {
        $token = Arr::get($credentials, 'api_token');

        throw_if(!$token, new Exception('Credentials array is missing api_token'));

        return $this->decoder->decodeToken($token)->getUser();
    }

    /**
     * @inheritDoc
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
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
