<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShortenedUrl extends Model
{

    protected $table = "shortened_urls";

    public $incrementing = false;

    public $timestamps = false;


    public function clicks(){
        return $this->hasMany('\App\Click', 'url', 'id');
    }

    public function owner(){
        if($this->owner == -1){
            return "Anonymous";
        }
        return User::whereId($this->owner)->first()->name;
    }

}
