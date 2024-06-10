<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use App\ContainerType;
//use App\State;
//use App\RegistrationType;

class ContainerTypeImport implements ToModel
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
            
            $ContainerType_name = isset($row[0]) ? trim($row[0]) : '';
            
            if(!empty($ContainerType_name)){
                $ContainerType_name_exist = ContainerType::where('container_type_name', $ContainerType_name)->first();
                if(empty($ContainerType_name_exist)){
                    $ContainerType = new ContainerType();
                    $ContainerType->container_type_name = (isset($ContainerType_name) && !empty($ContainerType_name)) ? $ContainerType_name : NULL;
                    $ContainerType->created_by = auth()->user()->id;
                    $ContainerType->updated_by = auth()->user()->id;
                    $ContainerType->save();
                }
            }
        }
        ++$this->row;
    }
}
