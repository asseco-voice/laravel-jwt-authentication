<?php


namespace Voice\Auth\App\Interfaces;


interface TokenUserInterface
{
    public function setFromClaims(array $claims): self;
    public function setStringToken(string $token): self;
}
