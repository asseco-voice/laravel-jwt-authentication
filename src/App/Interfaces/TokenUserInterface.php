<?php

namespace Asseco\Auth\App\Interfaces;

interface TokenUserInterface
{
    public function setFromClaims(array $claims): self;

    public function setStringToken(string $token): self;

    public function isServiceToken(): bool;

    public function getTokenAsString(): ?string;
}
