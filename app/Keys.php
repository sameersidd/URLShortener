<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Keys extends Model
{
    public function url()
    {
        return $this->belongsTo(URLs::class, 'url_id', 'id');
    }
}
