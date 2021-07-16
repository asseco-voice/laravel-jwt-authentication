<?php

namespace Asseco\Auth\App\Models;

use Asseco\Auth\App\Interfaces\TokenUserInterface;
use Asseco\Auth\App\Service\Decoder;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class TokenUser extends Model implements Authenticatable, TokenUserInterface
{
    private array $jwtData = [];

    public array $roles = [];

    public bool $fromToken = false;
    public bool $valid = false;

    public string $identifier;

    public string $clientIdentifier;

    public array $data = [];

    private array $claimMap;

    private string $token;

    private bool $isServiceToken = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->identifier = config('asseco-authentication.user_identifier');
        $this->claimMap = config('asseco-authentication.claim_map');
        $this->clientIdentifier = config('asseco-authentication.client_identifier');
    }

    /**
     * Set claims as properties.
     *
     * @param array $claims
     * @return $this
     */
    public function setFromClaims(array $claims): self
    {
        $this->fromToken = true;
        $this->extractData($claims);

        return $this;
    }

    /**
     * Add input string token.
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
        $this->{$this->identifier} = Arr::get($claims, $this->identifier);
        $this->{$this->clientIdentifier} = Arr::get($claims, $this->clientIdentifier);

        if ($this->{$this->clientIdentifier}) {
            $this->isServiceToken = true;
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
            $this->{$mapValue} = Arr::get($claims, $mapKey);
        }
    }

    public function getId(): ?string
    {
        return $this->getAuthIdentifier();
    }

    public function getTokenAsString(): ?string
    {
        return $this->token;
    }

    public function isServiceToken(): bool
    {
        return $this->isServiceToken;
    }

    /**
     * @param string $keyword
     * @return mixed|null
     */
    public function get(string $keyword)
    {
        return Arr::get($this->data, $keyword);
    }

    public function findRole(string $string)
    {
        return Arr::get($this->roles, $string);
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
        return $this->isServiceToken() ? $this->clientIdentifier : $this->identifier;
    }

    /**
     * @inheritDoc
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * @inheritDoc
     */
    public function getAuthPassword()
    {
        // TODO: Implement getAuthPassword() method.
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getRememberToken()
    {
        // TODO: Implement getRememberToken() method.
        return '';
    }

    /**
     * @inheritDoc
     */
    public function setRememberToken($value)
    {
        // TODO: Implement setRememberToken() method.
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getRememberTokenName()
    {
        // TODO: Implement getRememberTokenName() method.
        return '';
    }
}
