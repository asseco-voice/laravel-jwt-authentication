<?php

namespace Voice\Auth\App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchPublicKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asseco-voice:fetch-key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Iam public key from service';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {

            $response = Http::get(config('voice-auth.iam_key_url'));

            $jsonBody = $response->json();

        } catch (\Exception $exception) {
            echo "Failed fetching public key";
            echo $exception->getMessage();
            return 1;
        }

        $publicKey = '-----BEGIN PUBLIC KEY-----' . PHP_EOL . $jsonBody[config('voice-auth.public_key_array_location')] .PHP_EOL . '-----END PUBLIC KEY-----';

        $publicKeyFile = fopen(env('JWT_PUBLIC_KEY'), "w");

        fwrite($publicKeyFile, $publicKey);

        return 0;
    }
}
