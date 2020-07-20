<?php


namespace Voice\Auth\App;


use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Voice\Auth\App\Interfaces\TokenUserInterface;

class Decoder
{

    const JWT_IGNORE_CLAIMS = [
        "jti",
        "exp",
        "nbf",
        "iat",
        "iss",
        "aud",
        "sub",
        "typ",
        "azp",
        "auth_time",
        "session_state",
        "acr"
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
    /**
     * @var TokenUserInterface
     */
    private TokenUserInterface $user;
    /**
     * @var string
     */
    private string $keyLocation;


    public function __construct(string $keyLocation, TokenUserInterface $user)
    {
        $this->publicKey = new Key('file://' . $keyLocation);

        $this->signer = new Sha256();

        $this->builder = new Builder();

        $this->parser = new Parser();

        $this->user = $user;
        $this->keyLocation = $keyLocation;
    }

    public function decodeToken(string $token): self
    {
        $this->splitToken($token);
        $this->validToken = $this->verifyToken();
        return $this;
    }

    private function splitToken(string $token)
    {
        $parts = explode('.', $token);
        if (count($parts) != 3) {
            throw new \Exception("Shit not right!");
        }
        $this->headers = json_decode(base64_decode($parts[0]), true);
        $this->claims = json_decode(base64_decode($parts[1]), true);
        $this->signature = $parts[2];

        $this->token = $this->parser->parse((string)$token);
    }

    public function verifyToken(Token $token = null): bool
    {
        return $token ? $token->verify($this->signer, $this->publicKey) : $this->token->verify($this->signer, $this->publicKey);
    }

    public function getUser(): TokenUserInterface
    {
        if($this->validToken){
            return $this->user->setFromClaims($this->claims);
        }
    }

}
