<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use App\Quality;
use App\Commodity;

class QualityImport implements ToModel
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
            
            $commodity_name = isset($row[0]) ? trim($row[0]) : '';
            $commodity_id = '';
            $quality = isset($row[1]) ? trim($row[1]) : '';
            
            if(!empty($commodity_name)){
                $Commodity = Commodity::where('name', $commodity_name)->first();
                if(!empty($Commodity)){
                    $commodity_id = (isset($Commodity->id) && !empty($Commodity->id)) ? $Commodity->id : NULL;
                    if(!empty($commodity_id) && !empty($quality)){
                        $qualitydata = new Quality();
                        $qualitydata->quality = (isset($quality) && !empty($quality)) ? $quality : NULL;
                        $qualitydata->commodity_id = (isset($commodity_id) && !empty($commodity_id)) ? $commodity_id : NULL;
                        $qualitydata->created_by = auth()->user()->id;
                        $qualitydata->updated_by = auth()->user()->id;
                        $qualitydata->save();
                    }
                }
            }
        }
        ++$this->row;
    }
}
