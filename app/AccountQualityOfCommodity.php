<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountQualityOfCommodity extends Model
{
    protected $table = 'account_quality_of_commodity';

    // Relationship

    public function quality(){
       return $this->hasOne(Quality::class,'id','quality_id');
    }
}
