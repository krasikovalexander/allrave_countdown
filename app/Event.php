<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at', 'time'];

    public function drivers()
    {
        return $this->belongsToMany('App\User')->withPivot(['eta','manual','arrived'])->withTimestamps();
    }

    public function geocode($update = false)
    {
        $coords = $this->getCoordsByAddress($this->address);
        $this->lat = $coords["lat"];
        $this->lng = $coords["lng"];
        if ($update) {
            $this->save();
        }
        return $this->lat !== null;
    }

    public function reverseGeocode($update = false)
    {
        $address = $this->getAddressByCoords($this->lat, $this->lng);
        $this->address = $address;
        if ($update) {
            $this->save();
        }
        return $this->address !== null;
    }

    public function getAddressByCoords($lat, $lng)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, "http://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$lng&sensor=false");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $data = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($data);

        if (isset($data->status) && $data->status == 'OK') {
            if (count($data->results)) {
                return $data->results[0]->formatted_address;
            }
        }
        return null;
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

    public function calcEta($lat, $lng)
    {
        $duration = 0;
        if ($this->lat) {
            $url ="https://maps.googleapis.com/maps/api/directions/json?origin=$lat,$lng&destination={$this->lat},{$this->lng}&departure_time=now&key=".config("services.google.maps.api_key");

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $data = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($data);

            if (isset($data->status) && $data->status == 'OK') {
                if (count($data->routes)) {
                    foreach ($data->routes[0]->legs as $leg) {
                        $duration += ceil($leg->duration->value/60);
                    }
                    return $duration;
                }
            }
        }
        return $duration;
    }
}
