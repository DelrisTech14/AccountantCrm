<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use App\Expense;
use App\Commodity;
use App\ContainerType;
use App\Port;
use App\Currency;
use App\ExpensesName;
use App\ExpenseTypes;
use App\Lab;
use App\Gst;
use App\QuantityIn;
use App\Accounts;
use Config;

class ExpenseImport implements ToModel
{
    private $row = 0;
    public $not_inserted_data = array();
    public $master_inserted_data = array();
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if($this->row >= '2'){
            $buyer_name      = isset($row[0]) ? trim($row[0]) : '';
            $buyer_id        = '';
            
            $commodity_name      = isset($row[1]) ? trim($row[1]) : '';
            $commodity_id        = '';
            
            $container_type_name = isset($row[2]) ? trim($row[2]) : '';
            $container_type_id   = '';
            
            $lab_name            = isset($row[3]) ? trim($row[3]) : '';
            $lab_id              = '';
            
            $expense_name        = isset($row[4]) ? trim($row[4]) : '';
            $expense_name_id    = '';
            
            $expense_type_name        = isset($row[5]) ? trim($row[5]) : '';
            $expenses_type_id    = '';
            
            $quantity_in_name      = isset($row[6]) ? trim($row[6]) : '';
            $quantity_in_id        = '';
            
            $rate                = isset($row[7]) ? trim($row[7]) : '';
            
            $currency_name       = isset($row[8]) ? trim($row[8]) : '';
            $currency_id         = '';
            
            $gst_name            = isset($row[9]) ? trim($row[9]) : '';
            $gst_id              = '';
            
            $is_buyer_exist = 1;
            $is_expenses_type_exist = 1;
            
            if(!empty($commodity_name)){
                if(!empty($buyer_name)){
                    $buyer_exist = Accounts::where('name', $buyer_name)->where('group_id', Config::get('constants.ACCOUNT_GROUP.ID_BUYER'))->first();
                    if(!empty($buyer_exist)){
                        $buyer_id = (isset($buyer_exist->id) && !empty($buyer_exist->id)) ? $buyer_exist->id : NULL;
                    } else {
                        $is_buyer_exist = 0;
                        if(isset($this->not_inserted_data['Buyer'])){
                            if (in_array($buyer_name, $this->not_inserted_data['Buyer'])){ } else {
                                $this->not_inserted_data['Buyer'][] = $buyer_name;
                            }
                        } else {
                            $this->not_inserted_data['Buyer'][] = $buyer_name;
                        }
                    }
                }
                if(!empty($expense_type_name)){
                    $expenses_type = ExpenseTypes::where('expense_type_name', $expense_type_name)->first();
                    if(!empty($expenses_type)){
                        $expenses_type_id = (isset($expenses_type->id) && !empty($expenses_type->id)) ? $expenses_type->id : NULL;
                    } else {
                        $is_expenses_type_exist = 0;
                        if(isset($this->not_inserted_data['Expenses Type'])){
                            if (in_array($expense_type_name, $this->not_inserted_data['Expenses Type'])){ } else {
                                $this->not_inserted_data['Expenses Type'][] = $expense_type_name;
                            }
                        } else {
                            $this->not_inserted_data['Expenses Type'][] = $expense_type_name;
                        }
                    }
                }
                
                $commodity_exist = Commodity::where('name', $commodity_name)->first();
                if(empty($commodity_exist)){
                    if(isset($this->not_inserted_data['Commodity'])){
                        if (in_array($commodity_name, $this->not_inserted_data['Commodity'])){ } else {
                            $this->not_inserted_data['Commodity'][] = $commodity_name;
                        }
                    } else {
                        $this->not_inserted_data['Commodity'][] = $commodity_name;
                    }
                    
                }
                if(!empty($commodity_exist) && !empty($is_buyer_exist) && !empty($is_expenses_type_exist)){
                    $commodity_id = (isset($commodity_exist->id) && !empty($commodity_exist->id)) ? $commodity_exist->id : NULL;
                    if(!empty($container_type_name)){
                        $container_type = ContainerType::where('container_type_name', $container_type_name)->first();
                        if(empty($container_type)){
                            $container_type = new ContainerType();
                            $container_type->container_type_name = $container_type_name;
                            $container_type->created_by = auth()->user()->id;
                            $container_type->updated_by = auth()->user()->id;
                            $container_type->save();
                            $this->master_inserted_data['Container Type'][] = $container_type_name;
                        }
                        $container_type_id = (isset($container_type->id) && !empty($container_type->id)) ? $container_type->id : NULL;
                    }
                    if(!empty($lab_name)){
                        $lab = Lab::where('lab_name', $lab_name)->first();
                        if(empty($lab)){
                            $lab = new Lab();
                            $lab->lab_name = $lab_name;
                            $lab->created_by = auth()->user()->id;
                            $lab->updated_by = auth()->user()->id;
                            $lab->save();
                            $this->master_inserted_data['Lab Name'][] = $lab_name;
                        }
                        $lab_id = (isset($lab->id) && !empty($lab->id)) ? $lab->id : NULL;
                    }
                    if(!empty($expense_name)){
                        $expense = ExpensesName::where('expense_name', $expense_name)->first();
                        if(empty($expense)){
                            $expense = new ExpensesName();
                            $expense->expense_name = $expense_name;
                            $expense->created_by = auth()->user()->id;
                            $expense->updated_by = auth()->user()->id;
                            $expense->save();
                            $this->master_inserted_data['Expense Name'][] = $expense_name;
                        }
                        $expense_name_id = (isset($expense->id) && !empty($expense->id)) ? $expense->id : NULL;
                    }
                    if(!empty($gst_name)){
                        $gst = Gst::where('gst_name', $gst_name)->first();
                        if(empty($gst)){
                            $gst = new Gst();
                            $gst->gst_name = $gst_name;
                            $gst->created_by = auth()->user()->id;
                            $gst->updated_by = auth()->user()->id;
                            $gst->save();
                            $this->master_inserted_data['GST'][] = $gst_name;
                        }
                        $gst_id = (isset($gst->id) && !empty($gst->id)) ? $gst->id : NULL;
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
                            $this->master_inserted_data['Currency'][] = $currency_name;
                        }
                        $currency_id = (isset($currency->id) && !empty($currency->id)) ? $currency->id : NULL;
                    }
                    if($expenses_type_id == Config::get('constants.EXPENSE_TYPES.ID_WEIGHTS')){
                        if(!empty($quantity_in_name)){
                            $quantity_in = QuantityIn::where('quantity_in', $quantity_in_name)->first();
                            if(empty($quantity_in)){
                                $quantity_in = new QuantityIn();
                                $quantity_in->quantity_in = $quantity_in_name;
                                $quantity_in->created_by = auth()->user()->id;
                                $quantity_in->updated_by = auth()->user()->id;
                                $quantity_in->save();
                                $this->master_inserted_data['Quantity In'][] = $quantity_in_name;
                            }
                            $quantity_in_id = (isset($quantity_in->id) && !empty($quantity_in->id)) ? $quantity_in->id : NULL;
                        }
                    }
                    $expensem = new Expense();
                    $expensem->commodity_id = (isset($commodity_id) && !empty($commodity_id)) ? $commodity_id : NULL;
                    $expensem->buyer_id = (isset($buyer_id) && !empty($buyer_id)) ? $buyer_id : NULL;
                    $expensem->container_type_id = (isset($container_type_id) && !empty($container_type_id)) ? $container_type_id : NULL;
                    $expensem->rate = (isset($rate) && !empty($rate)) ? $rate : 0;
                    $expensem->currency_id = (isset($currency_id) && !empty($currency_id)) ? $currency_id : NULL;
                    $expensem->expense_type_id = (isset($expenses_type_id) && !empty($expenses_type_id)) ? $expenses_type_id : NULL;
                    $expensem->quantity_in_id = (isset($quantity_in_id) && !empty($quantity_in_id)) ? $quantity_in_id : NULL;
                    $expensem->gst = (isset($gst_name) && !empty($gst_name)) ? $gst_name : 0;
                    $expensem->expenses_name_id = (isset($expense_name_id) && !empty($expense_name_id)) ? $expense_name_id : NULL;
                    $expensem->lab_id = (isset($lab_id) && !empty($lab_id)) ? $lab_id : NULL;
                    $expensem->is_lab_expense = (isset($lab_id) && !empty($lab_id)) ? '1' : 0;
                    $expensem->created_by = auth()->user()->id;
                    $expensem->updated_by = auth()->user()->id;
                    $expensem->save();
                }
            }
        }
        ++$this->row;
    }
}
