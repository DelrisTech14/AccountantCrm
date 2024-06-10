<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use App\Packing;

class PackingImport implements ToModel
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
            
            $packing_type = isset($row[0]) ? trim($row[0]) : '';
            $cost_for_gross = isset($row[1]) ? trim($row[1]) : '';
            $cost_for_net = isset($row[2]) ? trim($row[2]) : '';
            
            if(!empty($packing_type)){
                $packing_type_exist = Packing::where('packing_type', $packing_type)->first();
                if(empty($packing_type_exist)){
                    $packing = new Packing();
                    $packing->packing_type = (isset($packing_type) && !empty($packing_type)) ? $packing_type : NULL;
                    $packing->cost_for_gross = (isset($cost_for_gross) && !empty($cost_for_gross)) ? $cost_for_gross : NULL;
                    $packing->cost_for_net = (isset($cost_for_net) && !empty($cost_for_net)) ? $cost_for_net : NULL;
                    $packing->created_by = auth()->user()->id;
                    $packing->updated_by = auth()->user()->id;
                    $packing->save();
                }
            }
        }
        ++$this->row;
    }
}
