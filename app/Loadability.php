<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loadability extends Model
{
    protected $guarder=['id'];

    public function commodity(){
        return $this->belongsTo(Commodity::class,'commodity_id','id');
    }
    public function quality(){
        return $this->belongsTo(Quality::class);
    }
    public function containerType(){
        return $this->belongsTo(ContainerType::class);
    }
    
}
