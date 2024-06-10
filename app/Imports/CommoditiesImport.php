<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use App\QuantityIn;
use App\Commodity;

class CommoditiesImport implements ToModel
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
            
            $commodity_name      = isset($row[0]) ? trim($row[0]) : '';
            
            $quantity_unit_name  = isset($row[1]) ? trim($row[1]) : '';
            $quantity_unit_id    = '';
            
            $price_unit_name     = isset($row[2]) ? trim($row[2]) : '';
            $price_unit_id       = '';
            
            if(!empty($commodity_name)){
                $commodity_name_exist = Commodity::where('name', $commodity_name)->first();
                if(empty($commodity_name_exist)){
                    if(!empty($quantity_unit_name)){
                        $quantity_unit = QuantityIn::where('quantity_in', $quantity_unit_name)->first();
                        if(empty($quantity_unit)){
                            $quantity_unit = new QuantityIn();
                            $quantity_unit->quantity_in = $quantity_unit_name;
                            $quantity_unit->created_by = auth()->user()->id;
                            $quantity_unit->updated_by = auth()->user()->id;
                            $quantity_unit->save();
                        }
                        $quantity_unit_id = (isset($quantity_unit->id) && !empty($quantity_unit->id)) ? $quantity_unit->id : NULL;
                    }
                    if(!empty($price_unit_name)){
                        $price_unit = QuantityIn::where('quantity_in', $price_unit_name)->first();
                        if(empty($price_unit)){
                            $price_unit = new QuantityIn();
                            $price_unit->quantity_in = $price_unit_name;
                            $price_unit->created_by = auth()->user()->id;
                            $price_unit->updated_by = auth()->user()->id;
                            $price_unit->save();
                        }
                        $price_unit_id = (isset($price_unit->id) && !empty($price_unit->id)) ? $price_unit->id : NULL;
                    }
                    
                    $commodity = new Commodity();
                    $commodity->name = (isset($commodity_name) && !empty($commodity_name)) ? $commodity_name : NULL;
                    $commodity->quantity_unit_id = (isset($quantity_unit_id) && !empty($quantity_unit_id)) ? $quantity_unit_id : NULL;
                    $commodity->price_unit_id = (isset($price_unit_id) && !empty($price_unit_id)) ? $price_unit_id : NULL;
                    $commodity->created_by = auth()->user()->id;
                    $commodity->updated_by = auth()->user()->id;
                    $commodity->save();
                }
            }
        }
        ++$this->row;
    }
}
