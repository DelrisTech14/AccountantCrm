<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use App\Freight;
use App\Commodity;
use App\ContainerType;
use App\Port;
use App\Currency;

class FreightImport implements ToModel
{
    private $row = 0;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
//        print_r($row); exit;
        if($this->row >= '1'){
            
            $commodity_name      = isset($row[0]) ? trim($row[0]) : '';
            $commodity_id        = '';
            $container_type_name = isset($row[1]) ? trim($row[1]) : '';
            $container_type_id   = '';
            $port_name           = isset($row[2]) ? trim($row[2]) : '';
            $port_id             = '';
            $freight_rate        = isset($row[3]) ? trim($row[3]) : '';
            $currency_name       = isset($row[4]) ? trim($row[4]) : '';
            $currency_id         = '';
            
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
                    if(!empty($port_name)){
                        $port = Port::where('name', $port_name)->first();
                        if(empty($port)){
                            $port = new Port();
                            $port->name = $port_name;
                            $port->is_destination = '1';
                            $port->created_by = auth()->user()->id;
                            $port->updated_by = auth()->user()->id;
                            $port->save();
                        }
                        $port_id = (isset($port->id) && !empty($port->id)) ? $port->id : NULL;
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
                    $freight = new Freight();
                    $freight->commodity_id = (isset($commodity_id) && !empty($commodity_id)) ? $commodity_id : NULL;
                    $freight->container_type_id = (isset($container_type_id) && !empty($container_type_id)) ? $container_type_id : NULL;
                    $freight->port_id = (isset($port_id) && !empty($port_id)) ? $port_id : NULL;
                    $freight->rate = (isset($freight_rate) && !empty($freight_rate)) ? $freight_rate :  0;
                    $freight->currency_id = (isset($currency_id) && !empty($currency_id)) ? $currency_id : NULL;
                    $freight->created_by = auth()->user()->id;
                    $freight->updated_by = auth()->user()->id;
                    $freight->save();
                }
            }
        }
        ++$this->row;
    }
}
