<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use App\Lab;

class LabImport implements ToModel
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
            
            $Lab_name = isset($row[0]) ? trim($row[0]) : '';
            
            if(!empty($Lab_name)){
                $Lab_name_exist = Lab::where('lab_name', $Lab_name)->first();
                if(empty($Lab_name_exist)){
                    $Lab = new Lab();
                    $Lab->lab_name = (isset($Lab_name) && !empty($Lab_name)) ? $Lab_name : NULL;
                    $Lab->created_by = auth()->user()->id;
                    $Lab->updated_by = auth()->user()->id;
                    $Lab->save();
                }
            }
        }
        ++$this->row;
    }
}
