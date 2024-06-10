<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use App\Port;
use App\State;
use App\Country;

class PortImport implements ToModel
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
            
            $port_name      = isset($row[0]) ? trim($row[0]) : '';
//            $state_name     = isset($row[1]) ? trim($row[1]) : '';
//            $state_id       = '';
            $country_name   = isset($row[1]) ? trim($row[1]) : '';
            $country_id     = '';
            $is_fob         = isset($row[2]) ? trim($row[2]) : '';
            $is_loading     = isset($row[3]) ? trim($row[3]) : '';
            $is_destination = isset($row[4]) ? trim($row[4]) : '';
            
            
            if(!empty($port_name)){
                $port_name_exist = Port::where('name', $port_name)->first();
                if(empty($port_name_exist)){
//                    if(!empty($state_name)){
//                        $state = State::where('name', $state_name)->first();
//                        if(empty($state)){
//                            $state = new State();
//                            $state->name = $state_name;
//                            $state->save();
//                        }
//                        $state_id = (isset($state->id) && !empty($state->id)) ? $state->id : NULL;
//                    }
                    if(!empty($country_name)){
                        $country = Country::where('name', $country_name)->first();
                        if(empty($country)){
                            $country = new Country();
                            $country->name = $country_name;
                            $country->created_by = auth()->user()->id;
                            $country->updated_by = auth()->user()->id;
                            $country->save();
                        }
                        $country_id = (isset($country->id) && !empty($country->id)) ? $country->id : NULL;
                    }
                    $port = new Port();
                    $port->name = (isset($port_name) && !empty($port_name)) ? $port_name : NULL;
                    $port->state_id = NULL;
                    $port->country = (isset($country_id) && !empty($country_id)) ? $country_id : NULL;
                    $port->is_fob = (isset($is_fob) && !empty($is_fob)) ? $is_fob : 0;
                    $port->is_loading = (isset($is_loading) && !empty($is_loading)) ? '1' : 0;
                    $port->is_destination = (isset($is_destination) && !empty($is_destination)) ? '1' : 0;
                    $port->created_by = auth()->user()->id;
                    $port->updated_by = auth()->user()->id;
                    $port->save();
                }
            }
        }
        ++$this->row;
    }
}
