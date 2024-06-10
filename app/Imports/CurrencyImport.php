<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use App\Currency;

class CurrencyImport implements ToModel
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
            
            $currency_name = isset($row[0]) ? trim($row[0]) : '';
            $currency_in_inr = isset($row[1]) ? trim($row[1]) : '';
            
            if(!empty($currency_name)){
                $currency_name_exist = Currency::where('currency', $currency_name)->first();
                if(empty($currency_name_exist)){
                    $currency = new Currency();
                    $currency->currency = (isset($currency_name) && !empty($currency_name)) ? $currency_name : NULL;
                    $currency->in_inr = (isset($currency_in_inr) && !empty($currency_in_inr)) ? $currency_in_inr : 0;
                    $currency->created_by = auth()->user()->id;
                    $currency->updated_by = auth()->user()->id;
                    $currency->save();
                }
            }
        }
        ++$this->row;
    }
}
