<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use DB;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'gender'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    const GENDERS = [
        1 => 'male',
        2 => 'female',

        'male' => 1,
        'female' => 2,
    ];

    /**
     * @Setter
     * Hashing password before saving
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * @Getter
     * getting gender as a string
     */
    public function getGenderAttribute($value)
    {
        return self::GENDERS[ $this->attributes[ 'gender' ] ];
    }

    /**
     * All the user's bonds
     */
    public function bonds()
    {
        return $this->belongsToMany('App\Bond', 'bond_user');
    }

    /**
     * All the user's pending bonds the he has sent
     */
    public function sentPendingBonds()
    {
        return $this->hasMany('App\PendingBond', 'sender_id');
    }

    /**
     * All the user's pending bonds the he has sent
     */
    public function receivedPendingBonds()
    {
        return $this->hasMany('App\PendingBond', 'receiver_id');
    }

    /**
     * All the user's bonds plucked into array
     * 1 - 11
     * 1 - 25
     * 1 - 17
     * 65 - 1
     * 
     * @returns [11,25,17,65]
     */
    public function getBondPartnersAttribute()
    {
        $bonds_ids_array = $this->bonds->pluck('id')->toArray();

        return DB::table('bond_user')->whereIn('bond_id', $bonds_ids_array)->where('user_id', '!=', $this->id)->pluck('user_id')->toArray();
    }

     /**
     * All the user's endeavors
     */
    public function endeavors()
    {
        return $this->belongsToMany('App\Endeavor', 'endeavor_user');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
