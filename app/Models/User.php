<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'USERS';


    public $incrementing = true;
    protected $primaryKey = 'id';

    
    protected $fillable = [
        'FIRST_NAME',
        'LAST_NAME',
        'FULL_NAME',
        'EMAIL',
        'PHONE',
        'ADDRESS',
        'DOB',
        'NID',
        'PASSWORD',
        'ROLE',
        'STATUS',
        'FATHER_NAME',
        'MOTHER_NAME',
        'GENDER',
        'PROFILE_PHOTO',
        'SIGNATURE',
    ];


    protected $hidden = [
        'PASSWORD',
    ];

 
   
    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {
        return $this->remember_token ?? $this->REMEMBER_TOKEN;
    }

    public function setRememberToken($value)
    {
        if (array_key_exists('remember_token', $this->getAttributes())) {
            $this->remember_token = $value;
        } else {
            $this->REMEMBER_TOKEN = $value;
        }
    }

    public function getRememberTokenName()
    {
        return array_key_exists('remember_token', $this->getAttributes()) ? 'remember_token' : 'REMEMBER_TOKEN';
    }

    public function accounts()
    {
        return $this->hasMany(Account::class, 'user_id', 'id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
