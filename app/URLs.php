<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class URLs extends Model
{
    protected $table = 'u_r_ls';
    /*
    * Returns the key stored in the db for the URL
    *
    * @return Key Model
    */
    public function key()
    {
        return $this->hasOne(Keys::class, 'url_id', 'id');
    }
}
