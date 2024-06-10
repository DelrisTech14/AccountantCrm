<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use App\Tolerance;

class ToleranceImport implements ToModel
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
            
            $tolerance_name = isset($row[0]) ? trim($row[0]) : '';
            
            if(!empty($tolerance_name)){
                $tolerance_name_exist = Tolerance::where('tolerance', $tolerance_name)->first();
                if(empty($tolerance_name_exist)){
                    $tolerance = new Tolerance();
                    $tolerance->tolerance = (isset($tolerance_name) && !empty($tolerance_name)) ? $tolerance_name : NULL;
                    $tolerance->save();
                }
            }
        }
        ++$this->row;
    }
}
