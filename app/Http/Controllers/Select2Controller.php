<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Accounts;
use App\DispatchThrough;
use App\Item;
use App\Company;
use App\Brand;
use App\ItemModel;
use App\Category;
use App\SubCategory;
use App\FumigationDosage;
use App\Packing;
use App\AccountCommodityWiseDefault;
use App\CommodityContainerWiseQuantity;
use App\Lab;
class Select2Controller extends Controller{

    function get_select2_data($table_name, $id_column, $text_column, $search, $page = 1, $where = array(),$where_in_column = '',$where_in_ids = array()) {
        $select2_data = array();
        $resultCount = 10;
        $offset = ($page - 1) * $resultCount;
        $results = DB::table("$table_name")
        ->where($text_column, 'LIKE', "%$search%")
        ->skip($offset)->take($resultCount)
        ->orderBy("$text_column")
        ->select("".$id_column."","".$text_column."")
        ->get();
        if (!empty($results)) {
            foreach ($results as $row) {
                $select2_data[] = array(
                    'id' => $row->$id_column,
                    'text' => $row->$text_column,
                );
            }
        }
        return $select2_data;
    }

    function count_select2_data($table_name, $id_column, $text_column, $search, $where = array(),$where_in_column = '',$where_in_ids = array()) {
        $results = DB::table("$table_name")
        ->where($text_column, 'LIKE', "%$search%")
        ->select("".$id_column."")
        ->get();
        return count($results);
    }

    function get_select2_text_by_id($table_name, $id_column, $text_column, $id_column_val) {
        $results = DB::table("$table_name")
        ->where($id_column, '=', "$id_column_val")
        ->select("".$id_column."","".$text_column."")
        ->get();
        if (!empty($results)) {
            echo json_encode(array('success' => true, 'id' => $id_column_val, 'text' => $results[0]->$text_column));
            exit();
        }
        echo json_encode(array('success' => true, 'id' => '', 'text' => '--select--'));
        exit();
    }

    function city_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('city', 'id', 'city_name', $search, $page),
            "total_count" => $this->count_select2_data('city', 'id', 'city_name', $search),
        );
        echo json_encode($results);
        exit();
    }

    function set_city_select2_val_by_id($id) {
        $this->get_select2_text_by_id('city', 'id', 'city_name', $id);
    }

    function area_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('area', 'id', 'area_name', $search, $page),
            "total_count" => $this->count_select2_data('area', 'id', 'area_name', $search),
        );
        echo json_encode($results);
        exit();
    }

    function set_area_select2_val_by_id($id) {
        $this->get_select2_text_by_id('area', 'id', 'area_name', $id);
    }

    function account_group_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('account_group', 'id', 'account_group_name', $search, $page),
            "total_count" => $this->count_select2_data('account_group', 'id', 'account_group_name', $search),
        );
        echo json_encode($results);
        exit();
    }

    function set_account_group_select2_val_by_id($id) {
        $this->get_select2_text_by_id('account_group', 'id', 'account_group_name', $id);
    }

//    function account_select2_source(Request $request) {
//        $search = isset($request->q) ? trim($request->q) : '';
//        $page = isset($request->page) ? trim($request->page) : 1;
//        $results = array(
//            "results" => $this->get_select2_data('account', 'id', 'account_name', $search, $page),
//            "total_count" => $this->count_select2_data('account', 'id', 'account_name', $search),
//        );
//        echo json_encode($results);
//        exit();
//    }
//
//    function set_account_select2_val_by_id($id) {
//        $this->get_select2_text_by_id('account', 'id', 'account_name', $id);
//    }

    function brand_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('brand', 'id', 'brand_name', $search, $page),
            "total_count" => $this->count_select2_data('brand', 'id', 'brand_name', $search),
        );
        echo json_encode($results);
        exit();
    }

    function brand_item_select2_source(Request $request) {
        $select2_data = array();
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $company_id = (isset($request->company_id) && !empty($request->company_id)) ? trim($request->company_id) : 0;
        $resultCount = 10;
        $offset = ($page - 1) * $resultCount;
        if(!empty($company_id)){
            $brands = Brand::select(DB::raw('brand.brand_name,brand.id'))
                    ->leftJoin('item', 'item.brand_id', '=', 'brand.id')
                    ->where('item.company_id',$company_id)
                    ->Where(function ($query) use ($search) {
                        $query->orWhere('brand.brand_name', 'LIKE', "%$search%");
                    })
                    ->groupBy('item.brand_id')->skip($offset)->take($resultCount)->get();
        } else {
            $brands = Brand::select(DB::raw('brand.brand_name,brand.id'))
                    ->leftJoin('item', 'item.brand_id', '=', 'brand.id')
                    ->Where(function ($query) use ($search) {
                        $query->orWhere('brand.brand_name', 'LIKE', "%$search%");
                    })
                    ->groupBy('item.brand_id')->skip($offset)->take($resultCount)->get();
        }
        if (!empty($brands)) {
            foreach ($brands as $brand) {
                $text = $brand->brand_name;
                $select2_data[] = array(
                    'id' => $brand->id,
                    'text' => $text,
                );
            }
        }
        $results = array(
            "results" => $select2_data,
            "total_count" => count($brands),
        );
        array_unshift($results['results'],array('id' => '0', 'text' => 'All'));
        echo json_encode($results);
        exit();
    }

    function set_brand_select2_val_by_id($id) {
        $this->get_select2_text_by_id('brand', 'id', 'brand_name', $id);
    }

    function model_from_company_and_brand_select2_source(Request $request) {
        $select2_data = array();
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $company_id = (isset($request->company_id) && !empty($request->company_id)) ? trim($request->company_id) : 0;
//        echo "<pre>"; print_r($company_id); exit;
        $brand_id = (isset($request->brand_id) && !empty($request->brand_id)) ? trim($request->brand_id) : 0;
        $resultCount = 10;
        $offset = ($page - 1) * $resultCount;
        $models = ItemModel::select(DB::raw('model.*'));
        $models->leftJoin('item', 'item.model_id', '=', 'model.id');
        if (!empty($company_id)) {
            $models->whereRaw('item.company_id="' . $company_id . '"');
        }
        if (!empty($brand_id)) {
            $models->where('item.brand_id','=',$brand_id);
        }
        $models->where('model.model_name', 'LIKE', "%$search%");
        $models->groupBy('item.model_id');
        $model_results = $models->skip($offset)->take($resultCount)->get();
        if (!empty($model_results)) {
            foreach ($model_results as $model) {
                $text = $model->model_name;
                $select2_data[] = array(
                    'id' => $model->id,
                    'text' => $text,
                );
            }
        }
        $results = array(
            "results" => $select2_data,
            "total_count" => $models->count(),
        );
        array_unshift($results['results'],array('id' => '0', 'text' => 'All'));
        echo json_encode($results);
        exit();
    }

    function category_from_company_and_brand_and_model_select2_source(Request $request) {
        $select2_data = array();
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $company_id = (isset($request->company_id) && !empty($request->company_id)) ? trim($request->company_id) : 0;
        $brand_id = (isset($request->brand_id) && !empty($request->brand_id)) ? trim($request->brand_id) : 0;
        $model_id = (isset($request->model_id) && !empty($request->model_id)) ? trim($request->model_id) : 0;
        $resultCount = 10;
        $offset = ($page - 1) * $resultCount;
        $categorys = Category::select(DB::raw('category.*'));
        $categorys->leftJoin('item', 'item.category_id', '=', 'category.id');
        if (!empty($company_id)) {
            $categorys->whereRaw('item.company_id="' . $company_id . '"');
        }
        if (!empty($brand_id)) {
            $categorys->where('item.brand_id','=',$brand_id);
        }
        if (!empty($model_id)) {
            $categorys->where('item.model_id','=',$model_id);
        }
        $categorys->where('category.category_name', 'LIKE', "%$search%");
        $categorys->groupBy('item.category_id');
        $category_results = $categorys->skip($offset)->take($resultCount)->get();
        if (!empty($category_results)) {
            foreach ($category_results as $category) {
                $text = $category->category_name;
                $select2_data[] = array(
                    'id' => $category->id,
                    'text' => $text,
                );
            }
        }
        $results = array(
            "results" => $select2_data,
            "total_count" => $categorys->count(),
        );
        array_unshift($results['results'],array('id' => '0', 'text' => 'All'));
        echo json_encode($results);
        exit();
    }

    function sub_category_from_company_and_brand_and_model_and_sub_category_select2_source(Request $request) {
        $select2_data = array();
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $company_id = (isset($request->company_id) && !empty($request->company_id)) ? trim($request->company_id) : 0;
        $brand_id = (isset($request->brand_id) && !empty($request->brand_id)) ? trim($request->brand_id) : 0;
        $model_id = (isset($request->model_id) && !empty($request->model_id)) ? trim($request->model_id) : 0;
        $category_id = (isset($request->category_id) && !empty($request->category_id)) ? trim($request->category_id) : 0;
        $resultCount = 10;
        $offset = ($page - 1) * $resultCount;
        $sub_categorys = SubCategory::select(DB::raw('sub_category.*'));
        $sub_categorys->leftJoin('item', 'item.sub_category_id', '=', 'sub_category.id');
        if (!empty($company_id)) {
            $sub_categorys->whereRaw('item.company_id="' . $company_id . '"');
        }
        if (!empty($brand_id)) {
            $sub_categorys->where('item.brand_id','=',$brand_id);
        }
        if (!empty($model_id)) {
            $sub_categorys->where('item.model_id','=',$model_id);
        }
        if (!empty($category_id)) {
            $sub_categorys->where('item.category_id','=',$category_id);
        }
        $sub_categorys->where('sub_category.sub_category_name', 'LIKE', "%$search%");
        $sub_categorys->groupBy('item.sub_category_id');
        $sub_category_results = $sub_categorys->skip($offset)->take($resultCount)->get();
        if (!empty($sub_category_results)) {
            foreach ($sub_category_results as $sub_category) {
                $text = $sub_category->sub_category_name;
                $select2_data[] = array(
                    'id' => $sub_category->id,
                    'text' => $text,
                );
            }
        }
        $results = array(
            "results" => $select2_data,
            "total_count" => $sub_categorys->count(),
        );
        array_unshift($results['results'],array('id' => '0', 'text' => 'All'));
        echo json_encode($results);
        exit();
    }

    function set_model_select2_val_by_id($id) {
        $this->get_select2_text_by_id('model', 'id', 'model_name', $id);
    }

    function model_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('model', 'id', 'model_name', $search, $page),
            "total_count" => $this->count_select2_data('model', 'id', 'model_name', $search),
        );
        echo json_encode($results);
        exit();
    }

    function category_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('category', 'id', 'category_name', $search, $page),
            "total_count" => $this->count_select2_data('category', 'id', 'category_name', $search),
        );
        array_unshift($results['results'],array('id' => '0', 'text' => 'All'));
        echo json_encode($results);
        exit();
    }

    function set_category_select2_val_by_id($id) {
        $this->get_select2_text_by_id('category', 'id', 'category_name', $id);
    }

    function sub_category_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('sub_category', 'id', 'sub_category_name', $search, $page),
            "total_count" => $this->count_select2_data('sub_category', 'id', 'sub_category_name', $search),
        );
        array_unshift($results['results'],array('id' => '0', 'text' => 'All'));
        echo json_encode($results);
        exit();
    }

    function set_sub_category_select2_val_by_id($id) {
        $this->get_select2_text_by_id('sub_category', 'id', 'sub_category_name', $id);
    }

    function item_select2_source(Request $request) {
        $select2_data = array();
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $company_id = (isset($request->company_id) && !empty($request->company_id)) ? trim($request->company_id) : 0;
        $brand_id = (isset($request->brand_id) && !empty($request->brand_id)) ? trim($request->brand_id) : 0;
        $model_id = (isset($request->model_id) && !empty($request->model_id)) ? trim($request->model_id) : 0;
        $category_id = (isset($request->category_id) && !empty($request->category_id)) ? trim($request->category_id) : 0;
        $sub_category_id = (isset($request->sub_category_id) && !empty($request->sub_category_id)) ? trim($request->sub_category_id) : 0;
//        echo "<pre>"; print_r($account_group_ids); exit;
        $resultCount = 10;
        $offset = ($page - 1) * $resultCount;
        $items = Item::select(DB::raw('item.id,item.item_name,item.item_code,item.location_1,company.company_name,brand.brand_name,model.model_name,category.category_name,sub_category.sub_category_name'));
        $items->leftJoin('company', 'company.id', '=', 'item.company_id');
        $items->leftJoin('brand', 'brand.id', '=', 'item.brand_id');
        $items->leftJoin('model', 'model.id', '=', 'item.model_id');
        $items->leftJoin('category', 'category.id', '=', 'item.category_id');
        $items->leftJoin('sub_category', 'sub_category.id', '=', 'item.sub_category_id');
        if (!empty($company_id)) {
            $items->whereRaw('item.company_id="' . $company_id . '"');
        }
        if (!empty($brand_id)) {
            $items->where('item.brand_id','=',$brand_id);
        }
        if (!empty($model_id)) {
            $items->where('item.model_id','=',$model_id);
        }
        if (!empty($category_id)) {
            $items->where('item.category_id','=',$category_id);
        }
        if (!empty($sub_category_id)) {
            $items->where('item.sub_category_id','=',$sub_category_id);
        }
        $items->Where(function ($query) use ($search) {
            $query->orWhere('item.item_name', 'LIKE', "%$search%")
                  ->orWhere('item.location_1', 'LIKE', "%$search%")
                  ->orWhere('company.company_name', 'LIKE', "%$search%")
                  ->orWhere('brand.brand_name', 'LIKE', "%$search%")
                  ->orWhere('model.model_name', 'LIKE', "%$search%")
                  ->orWhere('category.category_name', 'LIKE', "%$search%")
                  ->orWhere('sub_category.sub_category_name', 'LIKE', "%$search%")
                  ->orWhere('item.item_code', 'LIKE', "%$search%");
        });
        $items->groupBy('item.id');
        $items_results = $items->skip($offset)->take($resultCount)->get();
        if (!empty($items_results)) {
            foreach ($items_results as $item) {
                $text = '';
                $text .= $item->item_code;
                $text .= ' - '.$item->item_name;
                $text .= ' ( '.$item->location_1;
                $text .= ' - '.$item->company_name;
                $text .= ' - '.$item->brand_name;
                $text .= ' - '.$item->model_name;
                $text .= ' - '.$item->category_name;
                $text .= ' - '.$item->category_name.' ) ';

                $select2_data[] = array(
                    'id' => $item->id,
                    'text' => $text,
                );
            }
        }
        $results = array(
            "results" => $select2_data,
            "total_count" => $items->count(),
        );
        echo json_encode($results);
        exit();
    }

    function set_item_select2_val_by_id($id) {
        if (!empty($id)) {
            $item = Item::select(DB::raw('item.*,company.company_name,brand.brand_name,model.model_name,category.category_name,sub_category.sub_category_name'))
                    ->leftJoin('company', 'company.id', '=', 'item.company_id')
                    ->leftJoin('brand', 'brand.id', '=', 'item.brand_id')
                    ->leftJoin('model', 'model.id', '=', 'item.model_id')
                    ->leftJoin('category', 'category.id', '=', 'item.category_id')
                    ->leftJoin('sub_category', 'sub_category.id', '=', 'item.sub_category_id')
                    ->where('item.id', $id)->limit(1)->get();
            $formatted_item = [];
            if(!empty($item)){
                $item = $item[0];
                $text = '';
                $text .= $item->item_code;
                $text .= ' - '.$item->item_name;
                $text .= ' ( '.$item->company_name;
                $text .= ' - '.$item->brand_name;
                $text .= ' - '.$item->model_name;
                $text .= ' - '.$item->category_name;
                $text .= ' - '.$item->category_name.' ) ';
                $formatted_item = ['success' => true, 'id' => $item->id, 'text' => $text];
            } else {
                $formatted_item = ['success' => true, 'id' => '', 'text' => '--select--'];
            }
        }
        return \Response::json($formatted_item);
    }

    function invoice_type_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('invoice_type', 'id', 'invoice_type_name', $search, $page),
            "total_count" => $this->count_select2_data('invoice_type', 'id', 'invoice_type_name', $search),
        );
        echo json_encode($results);
        exit();
    }

    function set_invoice_type_select2_val_by_id($id) {
        $this->get_select2_text_by_id('invoice_type', 'id', 'invoice_type_name', $id);
    }

    function company_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('company', 'id', 'company_name', $search, $page),
            "total_count" => $this->count_select2_data('company', 'id', 'company_name', $search),
        );
        array_unshift($results['results'],array('id' => '0', 'text' => 'All'));
//        echo "<pre>"; print_r($results);
        echo json_encode($results);
        exit();
    }

    function set_company_select2_val_by_id($id) {
        $this->get_select2_text_by_id('company', 'id', 'company_name', $id);
    }

    function dispatch_through_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('dispatch_through', 'id', 'dispatch_through_name', $search, $page),
            "total_count" => $this->count_select2_data('dispatch_through', 'id', 'dispatch_through_name', $search),
        );
        echo json_encode($results);
        exit();
    }

    function set_dispatch_through_select2_val_by_id($id) {
        $this->get_select2_text_by_id('dispatch_through', 'id', 'dispatch_through_name', $id);
    }

    function account_select2_source(Request $request) {
        $select2_data = array();
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $account_group_ids = isset($request->ids) ? trim($request->ids) : '';
        $resultCount = 10;
        $offset = ($page - 1) * $resultCount;
        if(!empty($account_group_ids)){
            $accounts = Accounts::whereIn('group_id', explode(',',$account_group_ids))->where('name', 'LIKE', "%$search%")->skip($offset)->take($resultCount)->get();
        } else {
            $accounts = Accounts::where('name', 'LIKE', "%$search%")->skip($offset)->take($resultCount)->get();
        }
        if (!empty($accounts)) {
            foreach ($accounts as $account) {
                $text = $account->name;
                $select2_data[] = array(
                    'id' => $account->id,
                    'text' => $text,
                );
            }
        }
        $results = array(
            "results" => $select2_data,
            "total_count" => count($accounts),
        );
        echo json_encode($results);
        exit();
    }
    function change_lab_to_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('lab', 'id', 'lab_name', $search, $page),
            "total_count" => $this->count_select2_data('lab', 'id', 'lab_name', $search),
        );
		//print_r($results);
        echo json_encode($results);
        exit();
    }

    function set_account_select2_val_by_id($id = '') {
        if (!empty($id)) {
            $account = Accounts::where('id', $id)->limit(1)->get();
            $formatted_account = [];
            if(!empty($account)){
                $text = $account[0]->name;
                $formatted_account = ['success' => true, 'id' => $account[0]->id, 'text' => $text];
            } else {
                $formatted_account = ['success' => true, 'id' => '', 'text' => '--select--'];
            }
        }
        return \Response::json($formatted_account);
    }

    function set_dispatch_through_by_account_select2_val_by_id($id = '') {
        if (!empty($id)) {
            $dispatch_through = Account::select('dispatch_through.*')
                    ->leftJoin('dispatch_through', 'dispatch_through.id', '=', 'account.dispatch_through_id')
                    ->where('account.id',$id)->limit(1)->get();
            $formatted_dispatch_through = [];
            if(!empty($dispatch_through)){
                $text = $dispatch_through[0]->dispatch_through_name;
                $formatted_dispatch_through = ['success' => true, 'id' => $dispatch_through[0]->id, 'text' => $text];
            } else {
                $formatted_dispatch_through = ['success' => true, 'id' => '', 'text' => '--select--'];
            }
        }
        return \Response::json($formatted_dispatch_through);
    }

    function commodity_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('commodities', 'id', 'name', $search, $page),
            "total_count" => $this->count_select2_data('commodities', 'id', 'name', $search),
        );
        echo json_encode($results);
        exit();
    }

    function set_commodity_select2_val_by_id($id) {
        $this->get_select2_text_by_id('commodities', 'id', 'name', $id);
    }

    function quality_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('quality', 'id', 'quality', $search, $page),
            "total_count" => $this->count_select2_data('quality', 'id', 'quality', $search),
        );
        echo json_encode($results);
        exit();
    }

    function set_quality_select2_val_by_id($id) {
        $this->get_select2_text_by_id('quality', 'id', 'quality', $id);
    }

    function container_type_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('container_type', 'id', 'container_type_name', $search, $page),
            "total_count" => $this->count_select2_data('container_type', 'id', 'container_type_name', $search),
        );
        echo json_encode($results);
        exit();
    }

    function set_container_type_select2_val_by_id($id) {
        $this->get_select2_text_by_id('container_type', 'id', 'container_type_name', $id);
    }

    function currency_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('currency', 'id', 'currency', $search, $page),
            "total_count" => $this->count_select2_data('currency', 'id', 'currency', $search),
        );
        echo json_encode($results);
        exit();
    }

    function set_currency_select2_val_by_id($id) {
        $this->get_select2_text_by_id('currency', 'id', 'currency', $id);
    }

    function expense_type_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('expense_types', 'id', 'expense_type_name', $search, $page),
            "total_count" => $this->count_select2_data('expense_types', 'id', 'expense_type_name', $search),
        );
        echo json_encode($results);
        exit();
    }

    function set_expense_type_select2_val_by_id($id) {
        $this->get_select2_text_by_id('expense_types', 'id', 'expense_type_name', $id);
    }

    function expenses_name_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('expenses_name', 'id', 'expense_name', $search, $page),
            "total_count" => $this->count_select2_data('expenses_name', 'id', 'expense_name', $search),
        );
        echo json_encode($results);
        exit();
    }

    function set_expenses_name_select2_val_by_id($id) {
        $this->get_select2_text_by_id('expenses_name', 'id', 'expense_name', $id);
    }

    function comodity_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('commodities', 'id', 'name', $search, $page),
            "total_count" => $this->count_select2_data('commodities', 'id', 'name', $search),
        );
        echo json_encode($results);
        exit();
    }

    function quantity_in_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('quantity_in', 'id', 'quantity_in', $search, $page),
            "total_count" => $this->count_select2_data('quantity_in', 'id', 'quantity_in', $search),
        );
        echo json_encode($results);
        exit();
    }

    function set_quantity_in_select2_val_by_id($id) {
        $this->get_select2_text_by_id('quantity_in', 'id', 'quantity_in', $id);
    }

    function lab_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('lab', 'id', 'lab_name', $search, $page),
            "total_count" => $this->count_select2_data('lab', 'id', 'lab_name', $search),
        );
        echo json_encode($results);
        exit();
    }

    function set_lab_select2_val_by_id($id) {
        $this->get_select2_text_by_id('lab', 'id', 'lab_name', $id);
    }

    function gst_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('gst', 'gst_name', 'gst_name', $search, $page),
            "total_count" => $this->count_select2_data('gst', 'gst_name', 'gst_name', $search),
        );
        echo json_encode($results);
        exit();
    }

    function set_gst_select2_val_by_id($id) {
        $this->get_select2_text_by_id('gst', 'gst_name', 'gst_name', $id);
    }

    function packing_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('packing', 'id', 'packing_type', $search, $page),
            "total_count" => $this->count_select2_data('packing', 'id', 'packing_type', $search),
        );
        echo json_encode($results);
        exit();
    }

    function set_packing_select2_val_by_id($id)
     {
        $this->get_select2_text_by_id('packing', 'id', 'packing_type', $id);
    }
    

    function fumigation_dosage_select2_source(Request $request) {
        $select2_data = array();
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $con_type_id = isset($request->con_type_id) ? trim($request->con_type_id) : '';
//        echo "<pre>"; print_r($account_group_ids); exit;
        $resultCount = 10;
        $offset = ($page - 1) * $resultCount;
        $fumigation_dosages = FumigationDosage::select(DB::raw('fumigation_dosage.*, fumigation.fumigation_name'));
        $fumigation_dosages->leftJoin('fumigation', 'fumigation.id', '=', 'fumigation_dosage.fumigation_id');
        $fumigation_dosages->Where(function ($query) use ($search) {
            $query->orWhere('fumigation.fumigation_name', 'LIKE', "%$search%");
            $query->orWhere('fumigation_dosage.dosage', 'LIKE', "%$search%");
        });
        if(!empty($con_type_id)){
            $fumigation_dosages->where('fumigation_dosage.container_type_id','=',$con_type_id);
        }
        $fumigation_dosages->groupBy('fumigation_dosage.id');
        $fumigation_dosages_results = $fumigation_dosages->skip($offset)->take($resultCount)->get();
        if (!empty($fumigation_dosages_results)) {
            foreach ($fumigation_dosages_results as $fumigation_dosage) {
                $text = '';
                $text .= $fumigation_dosage->fumigation_name;
                $text .= ' - '.$fumigation_dosage->dosage;
                $select2_data[] = array('id' => $fumigation_dosage->id, 'text' => $text);
            }
        }
        $results = array(
            "results" => $select2_data,
            "total_count" => $fumigation_dosages->count(),
        );
        echo json_encode($results);
        exit();
    }

    function set_fumigation_dosage_select2_val_by_id($id) {
        if (!empty($id)) {
        $fumigation_dosage = FumigationDosage::select(DB::raw('fumigation_dosage.*, fumigation.fumigation_name'))
                        ->leftJoin('fumigation', 'fumigation.id', '=', 'fumigation_dosage.fumigation_id')
                        ->where('fumigation_dosage.id', $id)->limit(1)->get();
            $formatted = [];
            if(!empty($fumigation_dosage)){
                $fumigation_dosage = $fumigation_dosage[0];
                $text = '';
                $text .= $fumigation_dosage->fumigation_name;
                $text .= ' - '.$fumigation_dosage->dosage;
                $formatted = ['success' => true, 'id' => $fumigation_dosage->id, 'text' => $text];
            } else {
                $formatted = ['success' => true, 'id' => '', 'text' => '--select--'];
            }
        }
        return \Response::json($formatted);
    }

    function get_fumigation_dosage_val_by_id($id) {
        if (!empty($id)) {
        $fumigation_dosage = FumigationDosage::select(DB::raw('fumigation_dosage.*, fumigation.fumigation_name'))
                        ->leftJoin('fumigation', 'fumigation.id', '=', 'fumigation_dosage.fumigation_id')
                        ->where('fumigation_dosage.id', $id)->limit(1)->get();
            $formatted = [];
            if(!empty($fumigation_dosage)){
                $fumigation_dosage = $fumigation_dosage[0];
                $text = '';
                $text .= $fumigation_dosage->fumigation_name;
                $text .= ' - '.$fumigation_dosage->dosage;
                $formatted = ['success' => true, 'id' => $fumigation_dosage->id, 'text' => $text, 'cost' =>$fumigation_dosage->cost , 'currency_id'=>$fumigation_dosage->currency_id];
            } else {
                $formatted = ['success' => false, 'id' => '', 'text' => 'Data Not Found'];
            }
        }
        return \Response::json($formatted);
    }

    function get_packing_val_by_id($id,$ac_id=null,$commodity_id=null) {
        if (!empty($id)) {
        $packing = Packing::find($id);
        if(isset($ac_id) && $ac_id != '' && isset($commodity_id) && $commodity_id != ''){
            $ac_commodity_wise_default_data=AccountCommodityWiseDefault::where('account_id',$ac_id)->where('commodity_id',$commodity_id)->orderBy('id', 'DESC')->first();
        }
        $cost=0;
        $cost_type='';
            $formatted = [];
            if(!empty($packing)){
                if($ac_commodity_wise_default_data){
                    if(isset($ac_commodity_wise_default_data->cost_for) && $ac_commodity_wise_default_data->cost_for == 1){
                        $cost = $packing->cost_gross_no_gst;
                        $cost_type='Gross';
                    }else if(isset($ac_commodity_wise_default_data->cost_for) && $ac_commodity_wise_default_data->cost_for == 2){
                        $cost = $packing->cost_net_no_gst;
                        $cost_type='Net';
                    }
                }
                $formatted = ['success' => true, 'id' => $packing->id, 'packing_cost' => $cost,'cost_type'=>$cost_type , 'cost_net_no_gst' => $packing->cost_net_no_gst];
            } else {
                $formatted = ['success' => false, 'id' => '', 'text' => 'Data Not Found'];
            }
        }
        return \Response::json($formatted);
    }

    function port_select2_source(Request $request) {
        $search = isset($request->q) ? trim($request->q) : '';
        $page = isset($request->page) ? trim($request->page) : 1;
        $results = array(
            "results" => $this->get_select2_data('port', 'id', 'name', $search, $page),
            "total_count" => $this->count_select2_data('port', 'id', 'name', $search),
        );
        echo json_encode($results);
        exit();
    }

    function set_port_select2_val_by_id($id) {
        $this->get_select2_text_by_id('port', 'id', 'name', $id);
    }
    function set_quantityval_select2_val_by_id($id) {
       
        $this->get_select2_text_by_id('commodity_container_wise_quantity', 'id', 'quantity', $id);
    }
    
    
}
