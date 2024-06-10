<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Unit;
use Session;
use DB;
use Exception;
use PhpParser\Node\Stmt\TryCatch;
use Validator;

class QuantityInController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = array();
        return view('admin.master.quantityIn.index', $data);
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
            'quantity_in' => 'required|unique:quantity_in,quantity_in,' . $request->id,
        ];
        $validator = Validator::make($_POST, $rules);
        if ($validator->passes()) {
            if (!empty($request->id)) {
                $Unit = Unit::where('id', $request->id)->first();
                $Unit->updated_by = auth()->user()->id;
            } else {
                $Unit = new Unit();
                $Unit->created_by = auth()->user()->id;
                $Unit->updated_by = auth()->user()->id;
            }
            $Unit->quantity_in = !empty($request->quantity_in) ? $request->quantity_in : NULL;
            $Unit->save();
            if ($Unit->id) {
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
        $return['quantity_in_data'] = Unit::find($id);
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

    public function getQuantityInDatatable(Request $request)
    {
        $data = $request->all();
        $search_value = trim($data['search']['value']);
        $user = auth()->user();
        $dbQuery = Unit::select(DB::raw('*'))
            ->when($search_value, function ($dbQuery) use ($search_value, $request) {
                return $dbQuery->where(function ($dbQuery) use ($search_value, $request) {
                    /** @var Builder $dbQuery */
                    $preparedQ = '%' . $search_value . '%';
                    $num = 0;
                    foreach ([
                        'quantity_in',
                    ] as $field) {
                        if ($num) {
                            $dbQuery = $dbQuery->orWhere($field, 'LIKE', $preparedQ);
                        } else {
                            $dbQuery = $dbQuery->where($field, 'LIKE', $preparedQ);
                        }
                        $num++;
                    }
                    return $dbQuery;
                });
            });
        $dbQuery->orderBy('quantity_in', 'ASC');
        return datatables()->of($dbQuery)->toJson();
    }

    public function QuantityInDelete(Request $request)
    {
        try {
            $city = Unit::find($request->delete_quantity_in_id);
            $city->delete();
            Session::flash('status', 'success');
            Session::flash('message', 'Quantity In Successfully Deleted.');
        } catch (Exception $city) {
            Session::flash('status', 'danger');
            Session::flash('message', "Quantity In not allow to delete. Because it's Used.");
            return redirect()->back();
        }
        return redirect()->back();
    }
}
