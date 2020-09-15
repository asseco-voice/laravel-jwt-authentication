<?php

namespace Voice\Auth\App;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Voice\Auth\App\Interfaces\TokenUserInterface;

class TokenUser implements Authenticatable, TokenUserInterface
{
    private array $jwtData = [];

    public array $roles = [];

    public bool $fromToken = false;
    public bool $valid     = false;

    public string $identifier;

    public array $data = [];

    private array $claimMap;

    private string $token;

    public function __construct()
    {
        $this->identifier = config('asseco-authentication.user_identifier');
        $this->claimMap = config('asseco-authentication.claim_map');
    }

    /**
     * Set claims as properties
     *
     * @param array $claims
     * @return $this
     */
    public function setFromClaims(array $claims = []): self
    {
        $this->fromToken = true;
        $this->extractData($claims);
        return $this;
    }

    /**
     * Add input string token
     *
     * @param string $token
     * @return $this
     */
    public function setStringToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @param array $claims
     */
    private function extractData(array $claims = [])
    {
        if (isset($claims[$this->identifier])) {
            $this->{$this->identifier} = $claims[$this->identifier];
        } else {
            $this->{$this->identifier} = null;
        }

        if (isset($claims['voice_sys_validated'])) {
            $this->valid = $claims['voice_sys_validated'];
        }

        foreach ($claims as $claimKey => $claimValue) {
            if (in_array($claimKey, Decoder::JWT_IGNORE_CLAIMS)) {
                $this->jwtData[$claimKey] = $claimValue;
                continue;
            }
            $this->data[$claimKey] = $claimValue;
            if (strpos($claimKey, Decoder::ACCESS_KEYWORD) !== false) {
                $this->roles[$claimKey] = $claimValue;
            }
        }

        foreach ($this->claimMap as $mapKey => $mapValue) {
            $this->{$mapValue} = Arr::get($claims, $mapKey, null);
        }

    }

    public function getId(): ?string
    {
        return $this->{$this->identifier};
    }

    public function getTokenAsString():?string
    {
        return $this->token;
    }

    /**
     * @param string $keyword
     * @return mixed|null
     */
    public function get(string $keyword)
    {
        return Arr::get($this->data, $keyword, null);
    }

    public function findRole(string $string)
    {
        return Arr::get($this->roles, $string, null);
    }

    /**
     * @return bool
     */
    public function isFromToken(): bool
    {
        return $this->fromToken;
    }

    /**
     * @inheritDoc
     */
    public function getAuthIdentifierName()
    {
        // TODO: Implement getAuthIdentifierName() method.
    }

    /**
     * @inheritDoc
     */
    public function getAuthIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @inheritDoc
     */
    public function getAuthPassword()
    {
        // TODO: Implement getAuthPassword() method.
    }

    /**
     * @inheritDoc
     */
    public function getRememberToken()
    {
        // TODO: Implement getRememberToken() method.
    }

    /**
     * @inheritDoc
     */
    public function setRememberToken($value)
    {
        // TODO: Implement setRememberToken() method.
    }

    /**
     * @inheritDoc
     */
    public function getRememberTokenName()
    {
        // TODO: Implement getRememberTokenName() method.
    }
}
