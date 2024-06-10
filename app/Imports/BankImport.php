<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use App\Bank;

class BankImport implements ToModel
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
            
            $Bank_name = isset($row[0]) ? trim($row[0]) : '';
            $is_reference = isset($row[1]) ? trim($row[1]) : '';
            $branch = isset($row[2]) ? trim($row[2]) : '';
            $address = isset($row[3]) ? trim($row[3]) : '';
            $city = isset($row[4]) ? trim($row[4]) : '';
            $pincode = isset($row[5]) ? trim($row[5]) : '';
            $state = isset($row[6]) ? trim($row[6]) : '';
            $country = isset($row[7]) ? trim($row[7]) : '';
            $branch_no = isset($row[8]) ? trim($row[8]) : '';
            $phone = isset($row[9]) ? trim($row[9]) : '';
            $swift = isset($row[10]) ? trim($row[10]) : '';
            $ifsc = isset($row[11]) ? trim($row[11]) : '';
            $account_no = isset($row[12]) ? trim($row[12]) : '';
            $account_name = isset($row[13]) ? trim($row[13]) : '';
            
            if(!empty($Bank_name)){
                $Bank_name_exist = Bank::where('name', $Bank_name)->where('account_no', $account_no)->first();
                if(empty($Bank_name_exist)){
                    $Bank = new Bank();
                    $Bank->name = (isset($Bank_name) && !empty($Bank_name)) ? $Bank_name : NULL;
                    $Bank->is_reference = (isset($is_reference) && !empty($is_reference)) ? '1' : 0;
                    $Bank->branch = (isset($branch) && !empty($branch)) ? $branch : NULL;
                    $Bank->address = (isset($address) && !empty($address)) ? $address : NULL;
                    $Bank->city = (isset($city) && !empty($city)) ? $city : NULL;
                    $Bank->pincode = (isset($pincode) && !empty($pincode)) ? $pincode : NULL;
                    $Bank->state = (isset($state) && !empty($state)) ? $state : NULL;
                    $Bank->country = (isset($country) && !empty($country)) ? $country : NULL;
                    $Bank->branch_no = (isset($branch_no) && !empty($branch_no)) ? $branch_no : NULL;
                    $Bank->phone = (isset($phone) && !empty($phone)) ? $phone : NULL;
                    $Bank->swift = (isset($swift) && !empty($swift)) ? $swift : NULL;
                    $Bank->ifsc = (isset($ifsc) && !empty($ifsc)) ? $ifsc : NULL;
                    $Bank->account_no = (isset($account_no) && !empty($account_no)) ? $account_no : NULL;
                    $Bank->account_name = (isset($account_name) && !empty($account_name)) ? $account_name : NULL;
                    $Bank->created_by = auth()->user()->id;
                    $Bank->updated_by = auth()->user()->id;
                    $Bank->save();
                }
            }
        }
        ++$this->row;
    }
}
