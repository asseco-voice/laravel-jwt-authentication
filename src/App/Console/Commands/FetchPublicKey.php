<?php

namespace Asseco\Auth\App\Console\Commands;

use Asseco\Auth\App\Service\KeyFetcher;
use Exception;
use Illuminate\Console\Command;

class FetchPublicKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asseco:fetch-key';

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
    public function handle(KeyFetcher $keyFetcher)
    {
        try {
            $publicKeyLocation = $keyFetcher->fetch();
        } catch (Exception $e) {
            $this->error($e->getMessage());

            return 1;
        }

        $this->info('Public key stored into: ' . $publicKeyLocation);

        return 0;
    }
}
