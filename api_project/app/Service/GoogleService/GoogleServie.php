<?php
namespace App\Service\GoogleService;

use Illuminate\Support\Facades\Http;

class GoogleServie
{
    protected $googleService;

    public function __construct(Http $http)
    {
        $this->googleService = $http::withBasicAuth(
            config('app.google_user_name'),
            config('app.google_password'),
        );
    }

    public function check(string $receipt)
    {
        return $this->googleService->get(
            sprintf('%s/purchase/%s',  config('app.google_base_uri'), $receipt)
        )->json();
    }
}
