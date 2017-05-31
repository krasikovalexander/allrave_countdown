<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];

    public function drivers()
    {
        return $this->belongsToMany('App\User');
    }

    public function geocode($update = false)
    {
        $coords = $this->getCoordsByAddress($this->address);
        $this->lat = $coords["lat"];
        $this->lng = $coords["lng"];
        if ($update) {
            $this->save();
        }
    }

    public function getCoordsByAddress($address)
    {
        if ($address) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_URL, "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $data = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($data);

            if (isset($data->status) && $data->status == 'OK') {
                if (count($data->results)) {
                    return [
                        "lat" => $data->results[0]->geometry->location->lat,
                        "lng" => $data->results[0]->geometry->location->lng
                    ];
                }
            }
        }
        return ["lat" => null, "lng" => null];
    }
}
