<?php
namespace App\Service\IosService;

use Illuminate\Support\Facades\Http;

class IosService
{
    protected $iosService;

    public function __construct(Http $http)
    {
        $this->iosService = $http::withBasicAuth(
            config('app.ios_user_name'),
            config('app.ios_password'),
        );
    }

    public function check(string $receipt)
    {
        return $this->iosService->get(
            sprintf('%s/purchase/%s',  config('app.ios_base_uri'), $receipt)
        )->json();
    }
}
