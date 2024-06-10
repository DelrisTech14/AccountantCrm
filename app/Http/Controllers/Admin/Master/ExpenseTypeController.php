<?php

namespace App\Http\Controllers\Admin\Master;

use App\ExpenseTypes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use DB;
use Exception;
use PhpParser\Node\Stmt\TryCatch;
use Validator;

class ExpenseTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = array();
        return view('admin.master.expenseType.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        $rules = [
            'expense_type_name' => 'required|unique:expense_types,expense_type_name,' . $request->id,
        ];
        $validator = Validator::make($_POST, $rules);
        if ($validator->passes()) {
            if (!empty($request->id)) {
                $ExpenseTypes = ExpenseTypes::where('id', $request->id)->first();
                $ExpenseTypes->updated_by = auth()->user()->id;
            } else {
                $ExpenseTypes = new ExpenseTypes();
                $ExpenseTypes->created_by = auth()->user()->id;
                $ExpenseTypes->updated_by = auth()->user()->id;
            }
            $ExpenseTypes->expense_type_name = !empty($request->expense_type_name) ? $request->expense_type_name : NULL;
            $ExpenseTypes->save();
            if ($ExpenseTypes->id) {
                if (!empty($request->id)) {
                    $return['success'] = "Updated";
                } else {
                    $return['success'] = "Added";
                }
            }
        } else {
            $return['errors'] = $validator->errors()->all();
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
        $return = array();
        $return['expense_type_data'] = ExpenseTypes::find($id);
        print json_encode($return);
        exit;
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
    
    public function getExpenseTypeDatatable(Request $request)
    {
        $data = $request->all();
        $search_value = trim($data['search']['value']);
        $user = auth()->user();
        $cityQuery = ExpenseTypes::select(DB::raw('*'))
            ->when($search_value, function ($cityQuery) use ($search_value, $request) {
                return $cityQuery->where(function ($cityQuery) use ($search_value, $request) {
                    /** @var Builder $cityQuery */
                    $preparedQ = '%' . $search_value . '%';
                    $num = 0;
                    foreach ([
                        'expense_type_name',
                    ] as $field) {
                        if ($num) {
                            $cityQuery = $cityQuery->orWhere($field, 'LIKE', $preparedQ);
                        } else {
                            $cityQuery = $cityQuery->where($field, 'LIKE', $preparedQ);
                        }
                        $num++;
                    }
                    return $cityQuery;
                });
            });
        $cityQuery->orderBy('expense_type_name', 'ASC');
        return datatables()->of($cityQuery)->toJson();
    }
    public function ExpenseTypeDelete(Request $request)
    {
        try {
            $city = ExpenseTypes::find($request->delete_expense_type_id);
            $city->delete();
            Session::flash('status', 'success');
            Session::flash('message', 'Expense Type Successfully Deleted.');
        } catch (Exception $city) {
            Session::flash('status', 'danger');
            Session::flash('message', "Expense Type not allow to delete. Because it's Used.");
            return redirect()->back();
        }
        return redirect()->back();
    }
    
}
