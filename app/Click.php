<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Click extends Model
{

    protected $table = "clicks";

    public $incrementing = false;

    public $timestamps = false;
}
