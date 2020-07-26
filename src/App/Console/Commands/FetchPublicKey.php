<?php

namespace Voice\Auth\App\Console\Commands;

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
        if(!config('asseco-voice.authentication.iam_key_url')){
            echo 'Configuration missing. no IAM_KEY_URL set';
            return 1;
        }

        try {

            $response = Http::get(config('asseco-voice.authentication.iam_key_url'));

            $jsonBody = $response->json();

        } catch (\Exception $exception) {
            echo "Failed fetching public key";
            echo $exception->getMessage();
            return 1;
        }

        $publicKey = '-----BEGIN PUBLIC KEY-----' . PHP_EOL . $jsonBody[config('asseco-voice.authentication.public_key_array_location')] .PHP_EOL . '-----END PUBLIC KEY-----';

        $publicKeyFile = fopen(env('JWT_PUBLIC_KEY'), "w");

        fwrite($publicKeyFile, $publicKey);

        return 0;
    }
}
