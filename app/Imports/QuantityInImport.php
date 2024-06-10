<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use App\QuantityIn;

class QuantityInImport implements ToModel
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
            
            $quantity_in_name = isset($row[0]) ? trim($row[0]) : '';
            
            if(!empty($quantity_in_name)){
                $quantity_in_name_exist = QuantityIn::where('quantity_in', $quantity_in_name)->first();
                if(empty($quantity_in_name_exist)){
                    $quantity_in = new QuantityIn();
                    $quantity_in->quantity_in = (isset($quantity_in_name) && !empty($quantity_in_name)) ? $quantity_in_name : NULL;
                    $quantity_in->created_by = auth()->user()->id;
                    $quantity_in->updated_by = auth()->user()->id;
                    $quantity_in->save();
                }
            }
        }
        ++$this->row;
    }
}
