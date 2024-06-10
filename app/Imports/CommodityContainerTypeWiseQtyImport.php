<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use App\QuantityIn;
use App\Commodity;
use App\CommodityContainerWiseQuantity;
use App\ContainerType;

class CommodityContainerTypeWiseQtyImport implements ToModel
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
            $commodity_id      =  '';
            
            $container_type_name = isset($row[1]) ? trim($row[1]) : '';
            $container_type_id   = '';
            
            $qty = isset($row[2]) ? trim($row[2]) : '';
            
            if(!empty($commodity_name)){
                $commodity_exist = Commodity::where('name', $commodity_name)->first();
                if(!empty($commodity_exist)){
                    $commodity_id = (isset($commodity_exist->id) && !empty($commodity_exist->id)) ? $commodity_exist->id : NULL;
                    if(!empty($container_type_name)){
                        $container_type = ContainerType::where('container_type_name', $container_type_name)->first();
                        if(empty($container_type)){
                            $container_type = new ContainerType();
                            $container_type->container_type_name = $container_type_name;
                            $container_type->created_by = auth()->user()->id;
                            $container_type->updated_by = auth()->user()->id;
                            $container_type->save();
                        }
                        $container_type_id = (isset($container_type->id) && !empty($container_type->id)) ? $container_type->id : NULL;
                    }
                    $in_data = new CommodityContainerWiseQuantity();
                    $in_data->commodity_id = (isset($commodity_id) && !empty($commodity_id)) ? $commodity_id : NULL;
                    $in_data->container_type_id = (isset($container_type_id) && !empty($container_type_id)) ? $container_type_id : NULL;
                    $in_data->quantity = (isset($qty) && !empty($qty)) ? $qty : 0;
                    $in_data->created_by = auth()->user()->id;
                    $in_data->updated_by = auth()->user()->id;
                    $in_data->save();
                }
            }
        }
        ++$this->row;
    }
}

