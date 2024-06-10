<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Expense;
use App\Lab;
use App\Accounts;
use App\AccountCommodityWiseDefault;
use App\Currency;
use App\CommodityContainerWiseQuantity;
use Session;
use DB;
use Exception;
use PhpParser\Node\Stmt\TryCatch;
use Validator;
use App\Quality;
use App\AccountQualityOfCommodity;


class PricingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = array();
        return view('admin.pricing.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = array();
        $data['currency_data'] = Currency::find(\Config::get('constants.CURRENCY.ID_USD'));
        //$data['Quality_data'] = DB::table('quality')->select('*')->get();
        
        //print_r($Quality_data['quality']);
       
        return view('admin.pricing.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function destroy($id)
    {
        //
    }

    public function get_exp_data_from_commodity(Request $request){
        $results = array();
		//DB::enableQueryLog();
        if(isset($request->commodity_id) && !empty($request->commodity_id) && isset($request->buyer_id) && !empty($request->buyer_id)){
            $account_data = AccountCommodityWiseDefault::where('account_id', $request->buyer_id)->where('commodity_id', $request->commodity_id)->first();
            $expemse_query = Expense::select(DB::raw('expense.*,expense_types.expense_type_name,expense_types.id as extypid, expenses_name.expense_name, expense.gst as gst_name, lab.lab_name'))
                    ->leftJoin('expenses_name', 'expenses_name.id', '=', 'expense.expenses_name_id')
                    ->leftJoin('expense_types', 'expense_types.id', '=', 'expense.expense_type_id')
                    ->leftJoin('lab', 'lab.id', '=', 'expense.lab_id')
                    ->where('expense.commodity_id', $request->commodity_id)
                    ->where(function ($expemse_query) use ($request) {
                        $expemse_query->where('expense.buyer_id', $request->buyer_id)
                            ->orWhereNull('expense.buyer_id');
                    });
            if(!empty($account_data->lab_id))
            {

                $expemse_query->where(function ($expemse_query) use ($request, $account_data) {
                    $expemse_query->where('expense.lab_id', $account_data->lab_id)
                        ->orWhereNull('expense.lab_id');
                });
            }/* else {
                $expemse_query->whereNull('expense.lab_id');
            }*/

            if(isset($request->container_type_id) &&  !empty($request->container_type_id))
            {
                $expemse_query->where(function ($expemse_query) use ($request, $account_data) {
                    $expemse_query->where('expense.container_type_id', $request->container_type_id)
                        ->orWhereNull('expense.container_type_id');
                });

            }else{

                if(!empty($account_data->container_type_id)){
                    $expemse_query->where(function ($expemse_query) use ($request, $account_data) {
                        $expemse_query->where('expense.container_type_id', $account_data->container_type_id)
                            ->orWhereNull('expense.container_type_id');
                    });
                }

            }

            $expemse_query->orderBy('expense.buyer_id', 'DESC');
            $expemse_data = $expemse_query->get();
           //print_r($expemse_data); 
            $results = $expemse_data;
           // echo $expemse_query->toSql();
            //print_r($results);
        }

        echo json_encode($results);
        exit();
    }
    
    public function get_lab_data(Request $request)
    {
        $return = array();
        //$return['freight'] = '';
       
        if(isset($request->commodity_id) && !empty($request->commodity_id) && isset($request->buyer_id) && !empty($request->buyer_id)){
           // $data = Lab::where('id', $request->lab_id)->get();
           $return['lab_data']=Lab::where('id', $request->lab_id)->get();
            //print_r($return['lab_data'][0]['lab_name']);
            
           $expemse_query = Expense::select(DB::raw('expense.*,expense_types.expense_type_name,expense_types.id as extypid, expenses_name.expense_name, expense.gst as gst_name, lab.lab_name'))
                    ->leftJoin('expenses_name', 'expenses_name.id', '=', 'expense.expenses_name_id')
                    ->leftJoin('expense_types', 'expense_types.id', '=', 'expense.expense_type_id')
                    ->leftJoin('lab', 'lab.id', '=', 'expense.lab_id')
                    ->where('expense.commodity_id', $request->commodity_id)
                    ->where('expense.lab_id', $request->lab_id) 
                    ->where('expense.buyer_id', $request->buyer_id);
                    
                     
                   

                    $return['expemse_data_withbuyer'] = $expemse_query->get(); 
                    $expemse_query = Expense::select(DB::raw('expense.*,expense_types.expense_type_name,expense_types.id as extypid, expenses_name.expense_name, expense.gst as gst_name, lab.lab_name'))
                    ->leftJoin('expenses_name', 'expenses_name.id', '=', 'expense.expenses_name_id')
                    ->leftJoin('expense_types', 'expense_types.id', '=', 'expense.expense_type_id')
                    ->leftJoin('lab', 'lab.id', '=', 'expense.lab_id')
                    ->where('expense.commodity_id', $request->commodity_id)
                    ->where('expense.lab_id', $request->lab_id);
                    
                     
                   

                    $return['expemse_data_withoutbuyer'] = $expemse_query->get(); 

                    
           //
           if(isset($return['lab_data'][0]['lab_name']) && !empty($return['lab_data'][0]['lab_name'])){
             
                    $return['lab_name'] = $return['lab_data'][0]['lab_name'];
            }

        }
        //print_r($return);
        echo json_encode($return);
        exit;
    }
    public function get_commodity_container_wise_quantity(Request $request){
        $return = array();
        $return['freight'] = '';
        if(isset($request->commodity_id) && !empty($request->commodity_id) && isset($request->container_type_id) && !empty($request->container_type_id)){
            $data = CommodityContainerWiseQuantity::where('commodity_id', $request->commodity_id)->where('container_type_id', $request->container_type_id)->get();
            //print_r($data);
            if(isset($data[0]) && !empty($data[0])){
               // echo $data[0]->quantity;
                $return['quantity'] = $data[0]->quantity;
            }
        }
        echo json_encode($return);
        exit;
    }
   
    public function GetQuality(Request $request)
    {
        // Get the selected dropdown value
        $selectedValue = $request->input('selectedValue');

        // Fetch data based on the selected value
        //$data = YourModel::where('some_column', $selectedValue)->get();
        $data = Accounts::leftJoin('account_quality_of_commodity', 'Accounts.id', '=', 'account_quality_of_commodity.account_id')
        ->leftJoin('quality', 'quality.id', '=', 'account_quality_of_commodity.quality_id')
        ->where('account_quality_of_commodity.account_id', '=', $selectedValue)
        ->select('quality.id','quality.quality')
        ->get();

        // Return the data
        return response()->json($data);
    }
}
