<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bond extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sender_id', 'receiver_id'
    ];

    /**
     * All the users in the bond
     */
    public function users()
    {
        return $this->belongsToMany('App\User', 'bond_user');
    }

    /**
     * Getting the logged in bond's user
     * @return [type] [description]
     */
    public function getUserAttribute()
    {
        if( array_key_exists( 'user', $this->relations ) )
            return $this->relations[ 'user' ];

        $this->setRelation( 'user', $this->users->where('pivot.user_id', auth()->user()->id)->first() );
        return $this->relations[ 'user' ];
    }

    /**
     * Getting the other partner of the bond
     * @return [type] [description]
     */
    public function getPartnerAttribute()
    {
        if( array_key_exists( 'partner', $this->relations ) )
            return $this->relations[ 'partner' ];

        $this->setRelation( 'partner', $this->users->where('pivot.user_id', '!=' ,auth()->user()->id)->first() );
        return $this->relations[ 'partner' ];
    }

}
