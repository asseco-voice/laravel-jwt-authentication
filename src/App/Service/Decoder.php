<?php

namespace Asseco\Auth\App\Service;

use Asseco\Auth\App\Exceptions\InvalidTokenException;
use Asseco\Auth\App\Exceptions\TokenExpirationException;
use Asseco\Auth\App\Interfaces\TokenUserInterface;
use DateTime;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;


class Decoder
{
    public const JWT_IGNORE_CLAIMS = [
        'jti',
        'exp',
        'nbf',
        'iat',
        'iss',
        'aud',
        'sub',
        'typ',
        'azp',
        'auth_time',
        'session_state',
        'acr',
    ];

    public const ACCESS_KEYWORD = 'access';

    private InMemory $publicKey;
    private Token $token;
    private bool $validToken;
    private array $headers;
    private array $claims;
    private string $signature;
    private string $stringToken;

    private Configuration $configuration;

    /**
     * Decoder constructor.
     *
     * @param string $keyLocation
     * @param TokenUserInterface $user
     * @param KeyFetcher $keyFetcher
     */
    public function __construct(
        private string             $keyLocation,
        private TokenUserInterface $user,
        private KeyFetcher         $keyFetcher
    )
    {
        $this->configuration = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::file($this->keyLocation),
        );
    }

    /**
     * @param string $token
     * @return $this
     *
     * @throws InvalidTokenException
     * @throws TokenExpirationException
     * @throws \Exception
     */
    public function decodeToken(string $token): self
    {
        if (!file_exists($this->keyLocation)) {
            $this->keyLocation = $this->keyFetcher->fetch();
        }

        $this->publicKey = InMemory::file($this->keyLocation);
        $this->stringToken = $token;
        $this->splitToken($token);
        $this->validToken = $this->verifyToken();

        return $this;
    }

    /**
     * @param string $token
     */
    private function splitToken(string $token)
    {
        $this->token = $this->configuration->parser()->parse($token);
        $this->headers = $this->token->headers()->all();
        $this->claims = $this->token->claims()->all();
        $this->signature = $this->token->signature()->toString();
    }

    /**
     * @return bool
     *
     * @throws TokenExpirationException
     */
    private function verifyToken(): bool
    {
        if (!($this->tokenValid())) {
            return false;
        }

        if (!config('asseco-authentication.verify_expiration')) {
            throw new TokenExpirationException();
        }

        if (!isset($this->claims['exp']) || new DateTime() <= $this->claims['exp']) {
            return true;
        }

        if (config('asseco-authentication.throw_exception_on_invalid')) {
            throw new TokenExpirationException();
        }

        return false;
    }

    private function tokenValid(): bool
    {
        return $this->configuration->validator()->validate($this->token, ...$this->configuration->validationConstraints());
    }

    public function getUser(): TokenUserInterface
    {
        $this->claims['voice_sys_validated'] = $this->validToken;

        return $this->user->setFromClaims($this->claims)->setStringToken($this->token->toString());
    }
}
