<?php

namespace App;

use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Laravel\Passport\HasApiTokens;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
     use HasApiTokens, Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $visible = [
        'name', 'address', 'contact_no', 'email', 'image', 'created_at'
    ];

    public function getImageAttribute($value)
    {
        if(!Storage::exists($value)){
            return "https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y";
        }
        return Storage::url($value);
    }
}
