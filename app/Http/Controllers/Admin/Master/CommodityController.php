<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Commodity;
use Session;
use DB;
use Exception;
use PhpParser\Node\Stmt\TryCatch;
use Validator;

class CommodityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        return view('admin.master.commodity.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.master.commodity.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validate_rules = [
            'commodity_name' => 'required|unique:commodities,name,' . $request->id,
            'quantity_unit_id' => 'required',
            'price_unit_id' => 'required',
        ];
        request()->validate($validate_rules);
        
        $commodity = new Commodity();
        $commodity->name = $request->commodity_name;
        $commodity->quantity_unit_id = $request->quantity_unit_id;
        $commodity->price_unit_id = $request->price_unit_id;
        $commodity->created_by = auth()->user()->id;
        $commodity->updated_by = auth()->user()->id;
        $commodity->save();
        Session::flash('status', 'success');
        Session::flash('message', 'Commodity Successfully Created.');
        return redirect("admin/commodity");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Commodity  $commodity
     * @return \Illuminate\Http\Response
     */
    public function show(Commodity $commodity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Commodity  $commodity
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Commodity  $commodity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Commodity $commodity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Commodity  $commodity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Commodity $commodity)
    {
        //
    }

    public function getCommodityDatatable(Request $request)
    {
        $data = $request->all();
        $search_value = trim($data['search']['value']);
        $user = auth()->user();
        $commodityQuery = Commodity::select(DB::raw('commodities.*, qu.quantity_in as quantity_unit, pu.quantity_in as price_unit'))
            ->leftJoin(DB::raw("quantity_in as qu"), 'qu.id', '=', 'commodities.quantity_unit_id')
            ->leftJoin(DB::raw("quantity_in as pu"), 'pu.id', '=', 'commodities.price_unit_id')
            ->when($search_value, function ($commodityQuery) use ($search_value, $request) {
                return $commodityQuery->where(function ($commodityQuery) use ($search_value, $request) {
                    /** @var Builder $commodityQuery */
                    $preparedQ = '%' . $search_value . '%';
                    $num = 0;
                    foreach ([
                        'name',
                        'quantity_unit',
                        'price_unit',
                    ] as $field) {
                        if ($num) {
                            $commodityQuery = $commodityQuery->orWhere($field, 'LIKE', $preparedQ);
                        } else {
                            $commodityQuery = $commodityQuery->where($field, 'LIKE', $preparedQ);
                        }
                        $num++;
                    }
                    return $commodityQuery;
                });
            });
        $commodityQuery->orderBy('commodities.name', 'ASC');
        return datatables()->of($commodityQuery)->toJson();
    }
    public function CommodityDelete(Request $request)
    {
        try {
            $commodity = Commodity::find($request->delete_commodity_id);
            $commodity->delete();
            Session::flash('status', 'success');
            Session::flash('message', 'Commodity Successfully Deleted.');
        } catch (Exception $commodity) {
            Session::flash('status', 'danger');
            Session::flash('message', "Commodity not allow to delete. Because it's Used.");
            return redirect()->back();
        }
        return redirect()->back();
    }
}
