<?php

namespace App\Imports;

use App\Item;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Company;
use App\Brand;
use App\ItemModel;
use App\Category;
use App\SubCategory;
use DB;

class ItemImport implements ToModel
{
    private $row = 0;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
//        return new Item([
//            'item_code'     => $row[0],
//            'item_name'    => $row[1],
//        ]);
        if($this->row >= '7'){
            $item_code = isset($row[0]) ? trim($row[0]) : '';
            $item_name = isset($row[1]) ? trim($row[1]) : '';
            $gst_per = isset($row[2]) ? trim($row[2]) : '';
            $brand_name = isset($row[3]) ? trim($row[3]) : '';
            $category_name = isset($row[4]) ? trim($row[4]) : '';
            $location_1 = isset($row[5]) ? trim($row[5]) : '';
            $location_2 = isset($row[6]) ? trim($row[6]) : '';
            $current_qty = isset($row[7]) ? trim($row[7]) : '';
            $pending_so_qty = isset($row[8]) ? trim($row[8]) : '';
            $company_name = isset($row[9]) ? trim($row[9]) : '';
            $model_name = isset($row[10]) ? trim($row[10]) : '';
            $sub_category_name = isset($row[11]) ? trim($row[11]) : '';
            $gst_comodity = isset($row[12]) ? trim($row[12]) : '';
            $hsn_code = isset($row[13]) ? trim($row[13]) : '';
            $mrp = isset($row[14]) ? trim($row[14]) : '';
            $sales_rate = isset($row[15]) ? trim($row[15]) : '';
            $purchase_rate = isset($row[16]) ? trim($row[16]) : '';
            $sale_unit = isset($row[17]) ? trim($row[17]) : '';
            $purchase_unit = isset($row[18]) ? trim($row[18]) : '';
            $gst_unit = isset($row[19]) ? trim($row[19]) : '';

            if(!empty($item_name) || !empty($item_code)){
//                $is_exist = 0;
                if(!empty($item_code)){
                    $item = Item::where('item_code', $item_code)->first();
                    if(!empty($item)){
                        $item->updated_by = auth()->user()->id;
//                        $is_exist = 1;
                    } else {
                        $item = new Item();
                        $item->created_by = auth()->user()->id;
                        $item->updated_by = auth()->user()->id;
                    }
                } else {
                    if(!empty($item_name)){
                        $item = Item::where('item_name', $item_name)->first();
                        if(!empty($item)){
                            $item->updated_by = auth()->user()->id;
//                            $is_exist = 1;
                        } else {
                            $item = new Item();
                            $item->created_by = auth()->user()->id;
                            $item->updated_by = auth()->user()->id;
                        }
                    }
                }
//                if(empty($is_exist)){
                    // Insert Company
                    $company_id = NULL;
                    if(!empty($company_name)){
                        $company = Company::where('company_name', $company_name)->first();
                        if(empty($company)){
                            $company = new Company();
                            $company->company_name = $company_name;
                            $company->created_by = auth()->user()->id;
                            $company->updated_by = auth()->user()->id;
                            $company->save();
                        }
                        $company_id = (isset($company->id) && !empty($company->id)) ? $company->id : NULL;
                    }

                    // Insert Brand
                    if(!empty($brand_name)){
                        $brand = Brand::where('brand_name', $brand_name)->first();
                        if(empty($brand)){
                            $brand = new Brand();
                            $brand->brand_name = $brand_name;
                            $brand->company_id = $company_id;
                            $brand->created_by = auth()->user()->id;
                            $brand->updated_by = auth()->user()->id;
                            $brand->save();
                        }
                        $brand_id = (isset($brand->id) && !empty($brand->id)) ? $brand->id : NULL;
                    }

                    // Insert ItemModel
                    if(!empty($model_name)){
                        $itemmodel = ItemModel::where('model_name', $model_name)->first();
                        if(empty($itemmodel)){
                            $itemmodel = new ItemModel();
                            $itemmodel->model_name = $model_name;
                            $itemmodel->created_by = auth()->user()->id;
                            $itemmodel->updated_by = auth()->user()->id;
                            $itemmodel->save();
                        }
                        $model_id = (isset($itemmodel->id) && !empty($itemmodel->id)) ? $itemmodel->id : NULL;
                    }

                    // Insert Category
                    $category_id = NULL;
                    if(!empty($category_name)){
                        $category = Category::where('category_name', $category_name)->first();
                        if(empty($category)){
                            $category = new Category();
                            $category->category_name = $category_name;
                            $category->created_by = auth()->user()->id;
                            $category->updated_by = auth()->user()->id;
                            $category->save();
                        }
                        $category_id = (isset($category->id) && !empty($category->id)) ? $category->id : NULL;
                    }

                    // Insert SubCategory
                    if(!empty($sub_category_name)){
                        $sub_category = SubCategory::where('sub_category_name', $sub_category_name)->first();
    //                    $sub_category = DB::select(DB::raw("SELECT `id` FROM `sub_category` WHERE `sub_category_name` = '" . $sub_category_name . "' LIMIT 0,1"));
                        if(empty($sub_category)){
                            $sub_category = new SubCategory();
                            $sub_category->sub_category_name = $sub_category_name;
                            $sub_category->category_id = $category_id;
                            $sub_category->created_by = auth()->user()->id;
                            $sub_category->updated_by = auth()->user()->id;
                            $sub_category->save();
                        }
                        $sub_category_id = (isset($sub_category->id) && !empty($sub_category->id)) ? $sub_category->id : NULL;
                    }

                    $item->item_code = (isset($item_code) && !empty($item_code)) ? $item_code : NULL;
                    $item->item_name = $item_name;
                    $item->gst_per = (isset($gst_per) && !empty($gst_per)) ? $gst_per : NULL;
                    $item->brand_id = (isset($brand_id) && !empty($brand_id)) ? $brand_id : NULL;
                    $item->category_id = (isset($category_id) && !empty($category_id)) ? $category_id : NULL;
                    $item->location_1 = (isset($location_1) && !empty($location_1)) ? $location_1 : NULL;
                    $item->location_2 = (isset($location_2) && !empty($location_2)) ? $location_2 : NULL;
                    $item->current_qty = (isset($current_qty) && !empty($current_qty)) ? $current_qty : 0;
                    $item->pending_so_qty = (isset($pending_so_qty) && !empty($pending_so_qty)) ? $pending_so_qty : 0;
                    $item->company_id = (isset($company_id) && !empty($company_id)) ? $company_id : NULL;
                    $item->model_id = (isset($model_id) && !empty($model_id)) ? $model_id : NULL;
                    $item->sub_category_id = (isset($sub_category_id) && !empty($sub_category_id)) ? $sub_category_id : NULL;
                    $item->gst_comodity = (isset($gst_comodity) && !empty($gst_comodity)) ? $gst_comodity : NULL;
                    $item->hsn_code = (isset($hsn_code) && !empty($hsn_code)) ? $hsn_code : NULL;
                    $item->mrp = (isset($mrp) && !empty($mrp)) ? $mrp : 0;
                    $item->sales_rate = (isset($sales_rate) && !empty($sales_rate)) ? $sales_rate : 0;
                    $item->purchase_rate = (isset($purchase_rate) && !empty($purchase_rate)) ? $purchase_rate : 0;
                    $item->sale_unit = (isset($sale_unit) && !empty($sale_unit)) ? $sale_unit : NULL;
                    $item->purchase_unit = (isset($purchase_unit) && !empty($purchase_unit)) ? $purchase_unit : NULL;
                    $item->gst_unit = (isset($gst_unit) && !empty($gst_unit)) ? $gst_unit : NULL;
                    $item->opening_amount = 0;
                    $item->opening_qty = 0;
                    $item->pending_po_qty = 0;
                    $item->save();
//                }
            }
        }
        ++$this->row;
    }
}
