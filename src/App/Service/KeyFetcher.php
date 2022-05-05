<?php

namespace Asseco\Auth\App\Service;

use Asseco\Auth\App\Exceptions\MissingKeyUrl;
use Asseco\Auth\App\Exceptions\MissingResponseKey;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class KeyFetcher
{
    /**
     * Fetches the public key from an iam service and saves it on to a location set through configuration.
     *
     * @return string
     *
     * @throws RequestException
     * @throws Exception
     */
    public function fetch(): string
    {
        $iamKeyUrl = $this->getIamKeyUrl();
        $response = Http::get($iamKeyUrl)->throw()->json();
        $publicKey = $this->getPublicKey($response);

        $publicKeyLocation = config('asseco-authentication.public_key');
        $this->verifyAndCreateKeyLocation($publicKeyLocation);
        $publicKeyFile = fopen($publicKeyLocation, 'w');
        fwrite($publicKeyFile, $publicKey);

        return $publicKeyLocation;
    }

    /**
     * @return string
     * @throws MissingKeyUrl
     */
    protected function getIamKeyUrl(): string
    {
        $iamKeyUrl = config('asseco-authentication.iam_key_url');

        if (!$iamKeyUrl) {
            throw new MissingKeyUrl();
        }

        return $iamKeyUrl;
    }

    /**
     * @param mixed $response
     * @return string
     * @throws MissingResponseKey
     */
    protected function getPublicKey(mixed $response): string
    {
        $responseKey = config('asseco-authentication.public_key_array_location');
        $publicKey = Arr::get($response, $responseKey);

        if (!$publicKey) {
            throw new MissingResponseKey($responseKey);
        }

        return '-----BEGIN PUBLIC KEY-----' . PHP_EOL . $publicKey . PHP_EOL . '-----END PUBLIC KEY-----';
    }

    /**
     * Check if location exists, if not try to create it.
     *
     * @param string $location
     */
    private function verifyAndCreateKeyLocation(string $location)
    {
        $location = explode('/', $location);
        array_pop($location);
        $location = join('/', $location);

        if (!file_exists($location)) {
            mkdir($location, 0777, true);
        }
    }
}
