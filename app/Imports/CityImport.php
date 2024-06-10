<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use App\City;
//use App\State;
//use App\RegistrationType;

class CityImport implements ToModel
{
    private $row = 0;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
//        print_r($this->row); exit;
        if($this->row >= '1'){
            
            $city_name = isset($row[0]) ? trim($row[0]) : '';
            
            if(!empty($city_name)){
                $city_name_exist = City::where('city_name', $city_name)->first();
                if(empty($city_name_exist)){
                    $city = new City();
                    $city->city_name = (isset($city_name) && !empty($city_name)) ? $city_name : NULL;
                    $city->created_by = auth()->user()->id;
                    $city->updated_by = auth()->user()->id;
                    $city->save();
                }
            }
        }
        ++$this->row;
    }
}
