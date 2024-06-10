<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use App\ExpensesName;
//use App\State;
//use App\RegistrationType;

class ExpensesNameImport implements ToModel
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
            
            $ExpensesName_name = isset($row[0]) ? trim($row[0]) : '';
            
            if(!empty($ExpensesName_name)){
                $ExpensesName_name_exist = ExpensesName::where('expense_name', $ExpensesName_name)->first();
                if(empty($ExpensesName_name_exist)){
                    $ExpensesName = new ExpensesName();
                    $ExpensesName->expense_name = (isset($ExpensesName_name) && !empty($ExpensesName_name)) ? $ExpensesName_name : NULL;
                    $ExpensesName->created_by = auth()->user()->id;
                    $ExpensesName->updated_by = auth()->user()->id;
                    $ExpensesName->save();
                }
            }
        }
        ++$this->row;
    }
}
