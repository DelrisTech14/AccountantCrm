<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use App\Fumigation;
use App\FumigationDosage;
use App\ContainerType;
use App\Currency;

class FumigationDosageImport implements ToModel
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
            
            $fumigation_name = isset($row[0]) ? trim($row[0]) : '';
            $fumigation_id = '';
            $dosage = isset($row[1]) ? trim($row[1]) : '';
            $container_type_name = isset($row[2]) ? trim($row[2]) : '';
            $container_type_id = '';
            $cost = isset($row[3]) ? trim($row[3]) : '';
            $currency_name = isset($row[4]) ? trim($row[4]) : '';
            $currency_id = '';
            $gst = isset($row[5]) ? trim($row[5]) : '';
           
            
            if(!empty($fumigation_name)){
                
                $fumigation = Fumigation::where('fumigation_name', $fumigation_name)->first();
                if(empty($fumigation)){
                    $fumigation = new Fumigation();
                    $fumigation->fumigation_name = (isset($fumigation_name) && !empty($fumigation_name)) ? $fumigation_name : NULL;
                    $fumigation->created_by = auth()->user()->id;
                    $fumigation->updated_by = auth()->user()->id;
                    $fumigation->save();
                }
                $fumigation_id = (isset($fumigation->id) && !empty($fumigation->id)) ? $fumigation->id : NULL;
                
                if(!empty($container_type_name)){
                    $ContainerType = ContainerType::where('container_type_name', $container_type_name)->first();
                    if(empty($ContainerType)){
                        $ContainerType = new ContainerType();
                        $ContainerType->container_type_name = (isset($container_type_name) && !empty($container_type_name)) ? $container_type_name : NULL;
                        $ContainerType->created_by = auth()->user()->id;
                        $ContainerType->updated_by = auth()->user()->id;
                        $ContainerType->save();
                    }
                    $container_type_id = (isset($ContainerType->id) && !empty($ContainerType->id)) ? $ContainerType->id : NULL;
                }
                
                if(!empty($currency_name)){
                    $currency = Currency::where('currency', $currency_name)->first();
                    if(empty($currency)){
                        $currency = new Currency();
                        $currency->currency = $currency_name;
                        $currency->in_inr = '0.00';
                        $currency->created_by = auth()->user()->id;
                        $currency->updated_by = auth()->user()->id;
                        $currency->save();
                    }
                    $currency_id = (isset($currency->id) && !empty($currency->id)) ? $currency->id : NULL;
                }
                
                if(!empty($dosage)){
                    $FumigationDosage = new FumigationDosage();
                    $FumigationDosage->fumigation_id = (isset($fumigation_id) && !empty($fumigation_id)) ? $fumigation_id : NULL;
                    $FumigationDosage->dosage = (isset($dosage) && !empty($dosage)) ? $dosage : 0;
                    $FumigationDosage->container_type_id = (isset($container_type_id) && !empty($container_type_id)) ? $container_type_id : NULL;
                    $FumigationDosage->cost = (isset($cost) && !empty($cost)) ? $cost : 0;
                    $FumigationDosage->gst = (isset($gst) && !empty($gst)) ? $gst : 0;
                    $FumigationDosage->currency_id = (isset($currency_id) && !empty($currency_id)) ? $currency_id : NULL;
                    $FumigationDosage->created_by = auth()->user()->id;
                    $FumigationDosage->updated_by = auth()->user()->id;
                    $FumigationDosage->save();
                }
            }
        }
        ++$this->row;
    }
}
