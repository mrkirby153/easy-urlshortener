<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShortenedUrl extends Model
{

    protected $table = "shortened_urls";

    public $incrementing = false;

    public $timestamps = false;

}