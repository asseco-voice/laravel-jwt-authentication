<?php

namespace Voice\Auth\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class FetchPublicKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voice:fetch-key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch IAM public key from the service';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $iamKeyUrl = Config::get('asseco-authentication.iam_key_url');

        if (!$iamKeyUrl) {
            $this->error('Configuration missing. no IAM_KEY_URL set');

            return 1;
        }

        try {
            $response = Http::get($iamKeyUrl);
            $jsonBody = $response->json();
        } catch (\Exception $exception) {
            $this->error('Failed fetching public key');
            $this->error($exception->getMessage());

            return 1;
        }

        $responseKey = Config::get('asseco-authentication.public_key_array_location');
        if (!isset($jsonBody[$responseKey])) {
            $this->error("Unable to read $responseKey from response!'");

            return 1;
        }

        $publicKey = '-----BEGIN PUBLIC KEY-----' . PHP_EOL . $jsonBody[$responseKey] . PHP_EOL . '-----END PUBLIC KEY-----';
        $publicKeyLocation = Config::get('asseco-authentication.public_key');

        if (!$this->verifyAndCreateKeyLocation($publicKeyLocation)) {
            $this->error('Failed verifying location for public key!');

            return 1;
        }

        $publicKeyFile = fopen($publicKeyLocation, 'w');
        fwrite($publicKeyFile, $publicKey);
        $this->info('Public key from : ' . $iamKeyUrl . ' stored into : ' . $publicKeyLocation);

        return 0;
    }

    /**
     * Check if location exists, if not try to create it.
     *
     * @param string $location
     * @return bool
     */
    private function verifyAndCreateKeyLocation(string $location): bool
    {
        try {
            $location = explode('/', $location);
            array_pop($location);
            $location = join('/', $location);
            $this->info('Creating key location directory: ' . print_r($location, true));
            if (!file_exists($location)) {
                mkdir($location, 0777, true);
            }

            return true;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());

            return false;
        }
    }
}
