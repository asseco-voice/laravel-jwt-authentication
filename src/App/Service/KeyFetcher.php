<?php

namespace Asseco\Auth\App\Service;

use Illuminate\Support\Facades\Http;

class KeyFetcher
{
    /**
     * Fetches the public key from an iam service and saves it on to a location sed through configuration.
     *
     * @return string
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function fetch(): string
    {
        $iamKeyUrl = config('asseco-authentication.iam_key_url');

        if (!$iamKeyUrl) {
            throw new \Exception('Missing configuration: asseco-authentication.iam_key_url');
        }

        $response = Http::get($iamKeyUrl);
        $jsonBody = $response->throw()->json();

        $responseKey = config('asseco-authentication.public_key_array_location');
        if (!isset($jsonBody[$responseKey])) {
            throw new \Exception("Unable to read $responseKey from response!'");
        }

        $publicKey = '-----BEGIN PUBLIC KEY-----' . PHP_EOL . $jsonBody[$responseKey] . PHP_EOL . '-----END PUBLIC KEY-----';
        $publicKeyLocation = config('asseco-authentication.public_key');

        $this->verifyAndCreateKeyLocation($publicKeyLocation);

        $publicKeyFile = fopen($publicKeyLocation, 'w');
        fwrite($publicKeyFile, $publicKey);

        return $publicKeyLocation;
    }

    /**
     * Check if location exists, if not try to create it.
     *
     * @param  string  $location
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
