<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use App\PaymentTerms;

class PaymentTermsImport implements ToModel
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
            $payment_terms = isset($row[0]) ? trim($row[0]) : '';
            
            if(!empty($payment_terms)){
                $payment_terms_exist = PaymentTerms::where('payment_terms', $payment_terms)->first();
                if(empty($payment_terms_exist)){
                    $payment_termsd = new PaymentTerms();
                    $payment_termsd->payment_terms = (isset($payment_terms) && !empty($payment_terms)) ? $payment_terms : NULL;
                    $payment_termsd->save();
                }
            }
        }
        ++$this->row;
    }
}
