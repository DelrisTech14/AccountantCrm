<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Expense;
use Session;
use DB;
use Exception;
use PhpParser\Node\Stmt\TryCatch;
use Validator;

class ExpenseMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = array();
        return view('admin.master.expense.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        echo "<pre>"; print_r($_POST); exit;
        $return = array();
        if (!empty($request->id)) {
            $Expense = Expense::where('id', $request->id)->first();
            $Expense->updated_by = auth()->user()->id;
        } else {
            $Expense = new Expense();
            $Expense->created_by = auth()->user()->id;
            $Expense->updated_by = auth()->user()->id;
        }
        $Expense->buyer_id = (isset($request->buyer_id) && !empty($request->buyer_id)) ? $request->buyer_id : NULL;
        $Expense->commodity_id = (isset($request->commodity_id) && !empty($request->commodity_id)) ? $request->commodity_id : NULL;
        $Expense->container_type_id = (isset($request->container_type_id) && !empty($request->container_type_id)) ? $request->container_type_id : NULL;
        $Expense->rate = (isset($request->rate) && !empty($request->rate)) ? $request->rate : 0;
        $Expense->currency_id = (isset($request->currency_id) && !empty($request->currency_id)) ? $request->currency_id : NULL;
        $Expense->expense_type_id = (isset($request->expense_type_id) && !empty($request->expense_type_id)) ? $request->expense_type_id : NULL;
        $Expense->quantity_in_id = (isset($request->quantity_in_id) && !empty($request->quantity_in_id)) ? $request->quantity_in_id : NULL;
        $Expense->gst = (isset($request->gst) && !empty($request->gst)) ? $request->gst : 0;
        $Expense->is_lab_expense = (isset($request->is_lab_expense) && !empty($request->is_lab_expense)) ? 1 : 0;
        $Expense->lab_id = (isset($request->lab_id) && !empty($request->lab_id)) ? $request->lab_id : NULL;
        $Expense->expenses_name_id = (isset($request->expenses_name_id) && !empty($request->expenses_name_id)) ? $request->expenses_name_id : NULL;
        $Expense->save();
        if ($Expense->id) {
            if (!empty($request->id)) {
                $return['success'] = "Updated";
            } else {
                $return['success'] = "Added";
            }
        }
        print json_encode($return);
        exit;
    }

    public function getExpenseDatatable(Request $request)
    {
        $data = $request->all();
        $search_value = trim($data['search']['value']);
        $user = auth()->user();
        $Query = Expense::select(DB::raw('expense.*,accounts.name as buyer_name, commodities.name as commodity_name, container_type.container_type_name,currency.currency,expense_types.expense_type_name,expenses_name.expense_name,lab.lab_name, quantity_in.quantity_in as quantity_in_name, IF(expense.is_lab_expense = 1, "Yes", "No") as is_lab'))
                ->leftJoin(DB::raw("accounts"), 'accounts.id', '=', 'expense.buyer_id')
                ->leftJoin(DB::raw("commodities"), 'commodities.id', '=', 'expense.commodity_id')
                ->leftJoin(DB::raw("container_type"), 'container_type.id', '=', 'expense.container_type_id')
                ->leftJoin(DB::raw("currency"), 'currency.id', '=', 'expense.currency_id')
                ->leftJoin(DB::raw("expense_types"), 'expense_types.id', '=', 'expense.expense_type_id')
                ->leftJoin(DB::raw("quantity_in"), 'quantity_in.id', '=', 'expense.quantity_in_id')
                ->leftJoin(DB::raw("lab"), 'lab.id', '=', 'expense.lab_id')
                ->leftJoin(DB::raw("expenses_name"), 'expenses_name.id', '=', 'expense.expenses_name_id');
        if(!empty($data['buyer_id'])){
            $Query->whereRaw('expense.buyer_id="' . $data['buyer_id'] . '"');
        }
        if(!empty($data['commodity_id'])){
            $Query->whereRaw('expense.commodity_id="' . $data['commodity_id'] . '"');
        }
        if(!empty($data['container_type_id'])){
            $Query->whereRaw('expense.container_type_id="' . $data['container_type_id'] . '"');
        }
        $Query->orderBy('expense.created_at', 'DESC');
        return datatables()->of($Query)->toJson();
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
}
