<?php

namespace Asseco\Auth\App\Service;

use Asseco\Auth\App\Exceptions\InvalidTokenException;
use Asseco\Auth\App\Exceptions\TokenExpirationException;
use Asseco\Auth\App\Interfaces\TokenUserInterface;
use DateTime;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;

class Decoder
{
    const JWT_IGNORE_CLAIMS = [
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

    const ACCESS_KEYWORD = 'access';

    private Key $publicKey;
    private Rsa $signer;
    private Builder $builder;
    private Parser $parser;
    private Token $token;
    private bool $validToken;
    private array $headers;
    private array $claims;
    private string $signature;
    private TokenUserInterface $user;
    private string $keyLocation;
    private string $stringToken;
    private KeyFetcher $keyFetcher;

    /**
     * Decoder constructor.
     * @param string $keyLocation
     * @param TokenUserInterface $user
     * @param KeyFetcher $keyFetcher
     */
    public function __construct(
        string $keyLocation,
        TokenUserInterface $user,
        KeyFetcher $keyFetcher
    ) {
        $this->signer = new Sha256();

        $this->builder = new Builder();

        $this->parser = new Parser();

        $this->user = $user;
        $this->keyLocation = $keyLocation;

        $this->keyFetcher = $keyFetcher;
    }

    /**
     * @param string $token
     * @return $this
     * @throws InvalidTokenException
     * @throws TokenExpirationException
     * @throws \Exception
     */
    public function decodeToken(string $token): self
    {
        if (!file_exists($this->keyLocation)) {
            $this->keyLocation = $this->keyFetcher->fetch();
        }
        $this->publicKey = new Key('file://' . $this->keyLocation);

        $this->stringToken = $token;
        $this->splitToken($token);
        $this->validToken = $this->verifyToken();

        return $this;
    }

    /**
     * @param string $token
     * @throws InvalidTokenException
     */
    private function splitToken(string $token)
    {
        $parts = explode('.', $token);
        if (count($parts) != 3) {
            throw new InvalidTokenException();
        }
        $this->headers = json_decode(base64_decode($parts[0]), true);
        $this->claims = json_decode(base64_decode($parts[1]), true);
        $this->signature = $parts[2];

        $this->token = $this->parser->parse((string) $token);
    }

    /**
     * @return bool
     * @throws TokenExpirationException
     */
    private function verifyToken(): bool
    {
        $valid = $this->token->verify($this->signer, $this->publicKey);

        if (!$valid) {
            return false;
        }

        if (!config('asseco-authentication.verify_expiration')) {
            throw new TokenExpirationException();
        }

        $now = (new DateTime())->getTimestamp();

        if (!isset($this->claims['exp']) || $now <= $this->claims['exp']) {
            return true;
        }

        if (config('asseco-authentication.throw_exception_on_invalid')) {
            throw new TokenExpirationException();
        }

        return false;
    }

    public function getUser(): TokenUserInterface
    {
        $this->claims['voice_sys_validated'] = $this->validToken;

        return $this->user->setFromClaims($this->claims)->setStringToken($this->token);
    }
}
