<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\City;
use App\Contracts;
use App\Bank;
use App\Accounts;
use App\AccountGroups;
use App\AccountQualityOfCommodity;
use App\AccountCommodityWiseDefault;
use App\State;
use App\Commodity;
use App\Currency;
use App\Port;
use App\Country;
use App\Quality;
use App\FumigationDosage;
use App\Freight;
use Session;
use DB;
use Exception;
use PhpParser\Node\Stmt\TryCatch;
use Validator;
use Carbon\Carbon;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        $data['date'] = Carbon::now(); 
        $data['accounts'] = Accounts::all();
        
        return view('admin.account.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $accountgroups = AccountGroups::all();
        $countries = Country::all();
        $states = State::all();
        $cities = City::all();
        $banks = Bank::where('is_reference', '0')->get();
        $id_surveyor = \Config::get('constants.ACCOUNT_GROUP.ID_SURVEYOR');
        $surveyors = Accounts::select('accounts.name', 'accounts.id'
            )->join('account_groups','account_groups.id', 'accounts.group_id'
            )->where('account_groups.id', $id_surveyor)->get();
        $id_broker = \Config::get('constants.ACCOUNT_GROUP.ID_BROKER');
        $brokers = Accounts::select('accounts.name', 'accounts.id'
            )->join('account_groups','account_groups.id', 'accounts.group_id'
            )->where('account_groups.id', $id_broker)->get();
        $currencies = Currency::all();
        $ports = Port::all();
        $commodities = Commodity::all();
        $qualities = Quality::all();

        return view('admin.account.create',
            compact(
                'accountgroups', 'countries', 'states', 'cities', 'banks', 'surveyors', 'brokers', 'currencies', 'ports', 'commodities', 'qualities'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $return = array();

        $account = new Accounts();
        $account->name = $request->account_name;
        $account->group_id = $request->group_id;
        $account->phone = (isset($request->phone) && !empty($request->phone)) ? $request->phone : NULL;
        $account->mobile = (isset($request->mobile) && !empty($request->mobile)) ? $request->mobile : NULL;
        $account->whatsapp = (isset($request->whatsapp) && !empty($request->whatsapp)) ? $request->whatsapp : NULL;
        $account->country_id = (isset($request->country_id) && !empty($request->country_id)) ? $request->country_id : 0;
        $account->state_id = (isset($request->state_id) && !empty($request->state_id)) ? $request->state_id : 0;
        $account->city_id = (isset($request->city_id) && !empty($request->city_id)) ? $request->city_id : 0;
        $account->pincode = (isset($request->pincode) && !empty($request->pincode)) ? $request->pincode : NULL;
        $account->address = (isset($request->address) && !empty($request->address)) ? $request->address : NULL;
        $account->shipping_marks = (isset($request->shipping_marks) && !empty($request->shipping_marks)) ? $request->shipping_marks : NULL;
        $account->default_bank = (isset($request->default_bank) && !empty($request->default_bank)) ? $request->default_bank : 0;
        $account->surveyor_id = (isset($request->surveyor_id) && !empty($request->surveyor_id)) ? $request->surveyor_id : 0;
        $account->currency_id = (isset($request->currency_id) && !empty($request->currency_id)) ? $request->currency_id : 0;
        $account->default_port = (isset($request->default_port) && !empty($request->default_port)) ? $request->default_port : 0;
        $account->credit_days = (isset($request->credit_days) && !empty($request->credit_days)) ? $request->credit_days : NULL;
        $account->discount_per = (isset($request->discount_per) && !empty($request->discount_per)) ? $request->discount_per : NULL;
        $account->broker_id = (isset($request->broker_id) && !empty($request->broker_id)) ? $request->broker_id : NULL;
        $account->brokerage_per = (isset($request->brokerage_per) && !empty($request->brokerage_per)) ? $request->brokerage_per : NULL;
        $account->created_by = auth()->user()->id;
        $account->updated_by = auth()->user()->id;
        $account->save();
        if ($account->id) {

            if(isset($request->account_quality_of_commodity) && !empty($request->account_quality_of_commodity)){
                $account_quality_of_commoditys = json_decode($request->account_quality_of_commodity);
                foreach($account_quality_of_commoditys as $account_quality_of_commodity){
                    $add = new AccountQualityOfCommodity();
                    $add->account_id = $account->id;
                    $add->commodity_id = (isset($account_quality_of_commodity->commodity_id) && !empty($account_quality_of_commodity->commodity_id)) ? $account_quality_of_commodity->commodity_id : NULL;
                    $add->quality_id = (isset($account_quality_of_commodity->quality_id) && !empty($account_quality_of_commodity->quality_id)) ? $account_quality_of_commodity->quality_id : NULL;
                    $add->is_default = (isset($account_quality_of_commodity->is_default) && !empty($account_quality_of_commodity->is_default)) ? '1' : '0';
                    $add->save();
                }
            }

            if(isset($request->account_commodity_wise_default) && !empty($request->account_commodity_wise_default)){
                $account_commodity_wise_defaults = json_decode($request->account_commodity_wise_default);
                foreach($account_commodity_wise_defaults as $account_commodity_wise_default){
                    $add = new AccountCommodityWiseDefault();
                    $add->account_id = $account->id;
                    $add->commodity_id = (isset($account_commodity_wise_default->commodity_id) && !empty($account_commodity_wise_default->commodity_id)) ? $account_commodity_wise_default->commodity_id : NULL;
                    $add->packing_id = (isset($account_commodity_wise_default->packing_id) && !empty($account_commodity_wise_default->packing_id)) ? $account_commodity_wise_default->packing_id : NULL;
                    $add->cost_for = (isset($account_commodity_wise_default->cost_for) && !empty($account_commodity_wise_default->cost_for)) ? $account_commodity_wise_default->cost_for : NULL;
                    $add->fumigation_dosage_id = (isset($account_commodity_wise_default->fumigation_dosage_id) && !empty($account_commodity_wise_default->fumigation_dosage_id)) ? $account_commodity_wise_default->fumigation_dosage_id : NULL;
                    $add->container_type_id = (isset($account_commodity_wise_default->container_type_id) && !empty($account_commodity_wise_default->container_type_id)) ? $account_commodity_wise_default->container_type_id : NULL;
                    $add->lab_id = (isset($account_commodity_wise_default->lab_id) && !empty($account_commodity_wise_default->lab_id)) ? $account_commodity_wise_default->lab_id : NULL;
                    $add->save();
                }
            }

            if (!empty($request->id)) {
                $return['success'] = "Updated";
            } else {
                $return['success'] = "Added";
            }
        }
        print json_encode($return);
        exit;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    

    public function delete($id)
    {
        $record = Accounts::find($id);
        if ($record) {
            $record->delete();
            return redirect()->back()->with('success', 'Record deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Record not found.');
        }
    }
    public function get_account_data($account_id = ''){
        $return = array();
        if(!empty($account_id)){
            $return['account_data'] = Accounts::find($account_id);
        }
        echo json_encode($return);
        exit;
    }

    public function get_account_commodity_wise_default_data(Request $request)
    {
        $return = array();
        //print_r($request->container_type_id);
        if(isset($request->account_id) && !empty($request->account_id) && isset($request->commodity_id) && !empty($request->commodity_id)){
$data = AccountCommodityWiseDefault::where('account_id', $request->account_id)->where('commodity_id', $request->commodity_id)->limit(1)->get();
            $rate_data=AccountQualityOfCommodity::with('quality')->where('account_id', $request->account_id)->where('commodity_id', $request->commodity_id)->where('is_default',1)->orderBy('id', 'DESC')->first();
            if(!empty($rate_data))
             {
                $rate =$rate_data->quality->rate;
                $quality_name=$rate_data->quality->quality;
            }
             else
             {
                $rate_data=AccountQualityOfCommodity::with('quality')->where('account_id', $request->account_id)->where('commodity_id', $request->commodity_id)->orderBy('id', 'DESC')->first();
                if(!empty($rate_data))
                {
                    $rate =$rate_data->quality->rate;
                    $quality_name=$rate_data->quality->quality;
                }
            }


           // echo "<pre>";
            //print_r($rate_data);
             //die();
            if(isset($data[0])){
                $return['packing_id'] = $data[0]->packing_id;
                $return['fumigation_dosage_id'] = $data[0]->fumigation_dosage_id;
                $return['container_type_id'] = $data[0]->container_type_id;
                $return['lab_id'] = $data[0]->lab_id;
                $return['rate'] = (isset($rate) && $rate != '') ? $rate : 0;
                $return['quality_name'] = (isset($quality_name) && $quality_name != '') ? $quality_name : '';
            }
            $return['commodity_data'] = Commodity::find($request->commodity_id);
        }
        echo json_encode($return);
        exit;
    }

    public function get_fumigation_dosage_data(Request $request){
        $return = array();
        if(isset($request->container_type_id) && !empty($request->container_type_id) && isset($request->fumigation_dosage_id) && !empty($request->fumigation_dosage_id)){
            $data = FumigationDosage::where('id', $request->fumigation_dosage_id)->where('container_type_id', $request->container_type_id)->limit(1)->get();
            if(isset($data[0]) && !empty($data)){
                $return['rate'] = $data[0]->cost;
            }
        }
        echo json_encode($return);
        exit;
    }

    public function get_freight_data(Request $request)
    {
        $return = array();
        $return['freight'] = '';
        if(isset($request->commodity_id) && !empty($request->commodity_id) && isset($request->container_type_id) && !empty($request->container_type_id) && isset($request->destination_port_id) && !empty($request->destination_port_id)){
            $data = Freight::where('commodity_id', $request->commodity_id)->where('container_type_id', $request->container_type_id)->where('port_id', $request->destination_port_id)->get();
            if(isset($data[0]) && !empty($data)){
                $return['freight'] = $data[0]->rate;
            }
        }
        echo json_encode($return);
        exit;
    }
    public function get_quantity_data(Request $request)
    {
        $return = array();
        $return['quantity'] = '';
        if(isset($request->commodity_id) && !empty($request->commodity_id) && isset($request->container_type_id) && !empty($request->container_type_id) && isset($request->destination_port_id) && !empty($request->destination_port_id)){
            $data = CommodityContainerWiseQuantity::where('commodity_id', $request->commodity_id)->where('container_type_id', $request->container_type_id)->get();
            if(isset($data[0]) && !empty($data)){
                $return['quantity'] = $data[0]->quantity;
            }
        }
        echo json_encode($return);
        exit;
    }
}
