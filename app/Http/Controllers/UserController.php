<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    
    public function showNearestUsers()
    {
        // read the data from file

        $st = $this->readStringFromFile();
        $json = $this->getJsonFromString($st);

        $usersWithin100km = $this->findUsersWithDistanceLessThan100($json);

        //show users
        return view("nearest_users", compact('usersWithin100km') );
    }

    public function getJsonFromString($st)
    {
        // replace newlines with ,
        $st = str_replace("\n", ",", $st);
        // remove last comma
        $st = substr($st,0,strlen($st)-1);
        // add [ at start and ] at end
        $a = $st = "[".$st."]"; 
        // decode to json
        return json_decode($st, true);
    }
    public function readStringFromFile()
    {
        $path = storage_path() . "/app/public/affiliates.txt";
        return file_get_contents($path);
    }

    public function findUsersWithDistanceLessThan100($json)
    {
        $dublin = ['latitude' => 53.3340285, 'longitude' => -6.2535495];

        // find users
        $usersWithin100km = [];
        foreach ($json as $key => $user) {
            $distance = $this->getDistance($dublin['latitude'], $dublin['longitude'] , $user['latitude'], $user['longitude'] );
            if($distance <= 100) {
                $user['distance'] = round($distance,2);
                array_push($usersWithin100km, $user);
            }
        }
        return $usersWithin100km;
    }
    function getDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
    {
        $rad = M_PI / 180;
        //Calculate distance from latitude and longitude
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin($latitudeFrom * $rad) 
            * sin($latitudeTo * $rad) +  cos($latitudeFrom * $rad)
            * cos($latitudeTo * $rad) * cos($theta * $rad);

        return acos($dist) / $rad * 60 *  1.853;
    }
}
