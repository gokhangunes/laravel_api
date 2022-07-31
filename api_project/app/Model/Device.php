<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = ['uid', 'application_id', 'language', 'os', 'user_id'];

    protected $table = 'device';

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function application()
    {
        return $this->hasOne(Application::class);
    }
}
