<?php

namespace Voice\Auth\App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Voice\Auth\App\Decoder;
use Voice\Auth\App\Interfaces\TokenUserInterface;

class FetchServiceToken
{
    private Decoder $decoder;

    public function __construct(Decoder $decoder)
    {
        $this->decoder = $decoder;
    }

    public function getServiceUser(): TokenUserInterface
    {
        $url = Config::get('asseco-authentication.iam_key_url') . '/protocol/openid-connect/token';
        //TODO: currently broken because of outside constraints
        // $response = Http::asForm()->post($url, [
        //     "client_id" => env("CLIENT_ID", "livepoc_web"),
        //     "client_secret" => env("CLIENT_SECRET"),
        //     "grant_type" => env("GRANT_TYPE", "client_credentials")
        // ]);

        $response = Http::asForm()->post($url, [
            'client_id' => Config::get('asseco-authentication.client_id'),
            'client_secret' => Config::get('asseco-authentication.client_secret'),
            'grant_type' => 'password',
            'username'=>'live',
            'password'=>'live',
        ]);

        if ($response->failed()) {
            Log::error(print_r($response->json(), true));
            throw new \Exception($response->body(), $response->status());
        }
        $this->token = json_decode($response->body(), true)['access_token'];

        return $this->decoder->decodeToken($this->token)->getUser();
    }

    public function getServiceToken(): string
    {
        $url = Config::get('asseco-authentication.iam_key_url') . '/protocol/openid-connect/token';
        //TODO: currently broken because of outside constraints
        // $response = Http::asForm()->post($url, [
        //     "client_id" => env("CLIENT_ID", "livepoc_web"),
        //     "client_secret" => env("CLIENT_SECRET"),
        //     "grant_type" => env("GRANT_TYPE", "client_credentials")
        // ]);

        $response = Http::asForm()->post($url, [
            'client_id' => env('CLIENT_ID', 'livepoc_web'),
            'client_secret' => env('CLIENT_SECRET'),
            'grant_type' => 'password',
            'username'=>'live',
            'password'=>'live',
        ]);

        if ($response->failed()) {
            Log::error(print_r($response->json(), true));
            throw new \Exception($response->body(), $response->status());
        }

        return json_decode($response->body(), true)['access_token'];
    }
}
