<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use App\State;

class StateImport implements ToModel
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
            
            $State_name = isset($row[0]) ? trim($row[0]) : '';
            
            if(!empty($State_name)){
                $State_name_exist = State::where('name', $State_name)->first();
                if(empty($State_name_exist)){
                    $State = new State();
                    $State->name = (isset($State_name) && !empty($State_name)) ? $State_name : NULL;
                    $State->save();
                }
            }
        }
        ++$this->row;
    }
}
