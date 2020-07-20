<?php


namespace Voice\Auth\App;


use Illuminate\Contracts\Auth\Authenticatable;

class TokenUser implements Authenticatable
{
    private array $jwtData = [];

    public array $roles = [];

    public bool $fromToken = false;

    public string $identifier = "id";

    public array $data = [];

    /**
     * @var array
     */
    private array $claims;

    /**
     * @param array $claims
     * @return $this
     */
    public function setFromClaims(array $claims = []): self
    {
        $this->claims = $claims;
        $this->fromToken = true;
        $this->extractData($this->claims);
        return $this;
    }

    /**
     * @param array $claims
     */
    private function extractData(array $claims = [])
    {
        foreach ($claims as $claimKey => $claimValue){
            if (in_array($claimKey, Decoder::JWT_IGNORE_CLAIMS)){
                $this->jwtData[$claimKey] = $claimValue;
                continue;
            }
            $this->data[$claimKey] = $claimValue;
            if(strpos($claimKey, Decoder::ACCESS_KEYWORD) !== false){
                $this->roles[$claimKey] = $claimValue;
            }
        }
    }

    /**
     * @param string $keyword
     * @return mixed|null
     */
    public function get(string $keyword)
    {
        return isset($this->data[$keyword]) ? $this->data[$keyword] : null;
    }

    public function findRole(string $string)
    {
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
