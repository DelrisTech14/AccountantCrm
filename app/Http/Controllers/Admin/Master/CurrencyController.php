<?php

namespace App\Http\Controllers\Admin\Master;

use App\Currency;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use DB;
use Exception;
use PhpParser\Node\Stmt\TryCatch;
use Validator;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = array();
        return view('admin.master.currency.index', $data);
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
            'currency' => 'required|unique:currency,currency,' . $request->id,
        ];
        $validator = Validator::make($_POST, $rules);
        if ($validator->passes()) {
            if (!empty($request->id)) {
                $currency = Currency::where('id', $request->id)->first();
            } else {
                $currency = new Currency();
                $currency->created_by = auth()->user()->id;
            }
            $currency->currency = !empty($request->currency) ? $request->currency : NULL;
            $currency->updated_by = auth()->user()->id;
            $currency->save();
            if ($currency->id) {
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
        $return['currency_data'] = Currency::find($id);
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
    
    public function getCurrencyDatatable(Request $request)
    {
        $data = $request->all();
        $search_value = trim($data['search']['value']);
        $user = auth()->user();
        $currencyQuery = Currency::select(DB::raw('*'))
            ->when($search_value, function ($currencyQuery) use ($search_value, $request) {
                return $currencyQuery->where(function ($currencyQuery) use ($search_value, $request) {
                    /** @var Builder $currencyQuery */
                    $preparedQ = '%' . $search_value . '%';
                    $num = 0;
                    foreach ([
                        'currency',
                    ] as $field) {
                        if ($num) {
                            $currencyQuery = $currencyQuery->orWhere($field, 'LIKE', $preparedQ);
                        } else {
                            $currencyQuery = $currencyQuery->where($field, 'LIKE', $preparedQ);
                        }
                        $num++;
                    }
                    return $currencyQuery;
                });
            });
        $currencyQuery->orderBy('currency', 'ASC');
        return datatables()->of($currencyQuery)->toJson();
    }
    public function CurrencyDelete(Request $request)
    {
        try {
            $currency = Currency::find($request->delete_currency_id);
            $currency->delete();
            Session::flash('status', 'success');
            Session::flash('message', 'Currency Successfully Deleted.');
        } catch (Exception $currency) {
            Session::flash('status', 'danger');
            Session::flash('message', "Currency not allow to delete. Because it's Used.");
            return redirect()->back();
        }
        return redirect()->back();
    }
    
}
