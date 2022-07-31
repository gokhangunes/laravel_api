<?php
namespace App\Service\HttpService;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HttpService
{
    protected $httpClient;

    public function __construct(Http $http)
    {
        $this->httpClient = $http::withBasicAuth(
            config('app.ios_user_name'),
            config('app.ios_password'),
        );
    }

    public function call(string $url, array $data)
    {
        Log::info("HELALL");
        return $this->httpClient->post(
            $url,
            $data
        )->status();
    }
}
