<?php

namespace App;

use App\Model\Device;
use App\Model\Subscription;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['client-token'];

    public function devices()
    {
        return $this->hasOne(Device::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }
}
