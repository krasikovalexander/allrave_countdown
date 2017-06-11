<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];
    protected $appends = ['eta'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function isAdmin()
    {
        return $this->admin;
    }

    public function getEtaAttribute()
    {
        $eta = $this->pivot->arrived ? 0 : $this->pivot->eta;
        if ($this->pivot->manual && !$this->pivot->arrived) {
            $eta = $this->pivot->eta - $this->pivot->updated_at->diffInMinutes(Carbon::now());
        }
        return $eta;
    }
}
