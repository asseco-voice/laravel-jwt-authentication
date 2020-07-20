<?php


namespace Voice\Auth\App\Interfaces;


interface TokenUserInterface
{
    public function setFromClaims(array $claims);
}
