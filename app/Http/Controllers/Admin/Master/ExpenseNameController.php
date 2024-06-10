<?php

namespace App\Http\Controllers\Admin\Master;

use App\ExpensesName;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use DB;
use Exception;
use PhpParser\Node\Stmt\TryCatch;
use Validator;

class ExpenseNameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = array();
        return view('admin.master.expenseName.index', $data);
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
            'expense_name' => 'required|unique:expenses_name,expense_name,' . $request->id,
        ];
        $validator = Validator::make($_POST, $rules);
        if ($validator->passes()) {
            if (!empty($request->id)) {
                $expense_name = ExpensesName::where('id', $request->id)->first();
                $expense_name->updated_by = auth()->user()->id;
            } else {
                $expense_name = new ExpensesName();
                $expense_name->created_by = auth()->user()->id;
                $expense_name->updated_by = auth()->user()->id;
            }
            $expense_name->expense_name = !empty($request->expense_name) ? $request->expense_name : NULL;
            $expense_name->save();
            if ($expense_name->id) {
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
        $return['expense_name_data'] = ExpensesName::find($id);
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
    
    public function getExpenseNameDatatable(Request $request)
    {
        $data = $request->all();
        $search_value = trim($data['search']['value']);
        $user = auth()->user();
        $cityQuery = ExpensesName::select(DB::raw('*'))
            ->when($search_value, function ($cityQuery) use ($search_value, $request) {
                return $cityQuery->where(function ($cityQuery) use ($search_value, $request) {
                    /** @var Builder $cityQuery */
                    $preparedQ = '%' . $search_value . '%';
                    $num = 0;
                    foreach ([
                        'expense_name',
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
        $cityQuery->orderBy('expense_name', 'ASC');
        return datatables()->of($cityQuery)->toJson();
    }
    public function ExpenseNameDelete(Request $request)
    {
        try {
            $city = ExpensesName::find($request->delete_expense_name_id);
            $city->delete();
            Session::flash('status', 'success');
            Session::flash('message', 'Expense Name Successfully Deleted.');
        } catch (Exception $city) {
            Session::flash('status', 'danger');
            Session::flash('message', "Expense Name not allow to delete. Because it's Used.");
            return redirect()->back();
        }
        return redirect()->back();
    }
    
}
