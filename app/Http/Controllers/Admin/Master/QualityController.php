<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Quality;
use App\Commodity;
use Session;
use DB;
use Exception;
use PhpParser\Node\Stmt\TryCatch;
use Validator;

class QualityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = Quality::all();
        return view('admin.master.quality.index', compact('data'));
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
            'quality' => 'required|unique:quality,quality,' . $request->id,
        ];
        $validator = Validator::make($_POST, $rules);
        if ($validator->passes()) {
            if (!empty($request->id)) {
                $quality = Quality::where('id', $request->id)->first();
                $quality->updated_by = auth()->user()->id;
            } else {
                $quality = new Quality();
                $quality->created_by = auth()->user()->id;
                $quality->updated_by = auth()->user()->id;
            }
            $quality->commodity_id = !empty($request->commodity_id) ? $request->commodity_id : NULL;
            $quality->quality = !empty($request->quality) ? $request->quality : NULL;
            $quality->save();
            if ($quality->id) {
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
     * @param  \App\City  $city
     * @return \Illuminate\Http\Response
     */
    public function show(City $city)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\City  $city
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $return = array();
        $return['quality_data'] = Quality::find($id);
        print json_encode($return);
        exit;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\City  $city
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, City $city)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\City  $city
     * @return \Illuminate\Http\Response
     */
    public function destroy(City $city)
    {
        //
    }

    public function getQualityDatatable(Request $request)
    {
        $data = $request->all();
        $search_value = trim($data['search']['value']);
        $user = auth()->user();
        $dbQuery = Quality::select(DB::raw('quality.*, commodities.name as commodity_name'))
            ->leftJoin('commodities', 'commodities.id', '=', 'quality.commodity_id')
            ->when($search_value, function ($dbQuery) use ($search_value, $request) {
                return $dbQuery->where(function ($dbQuery) use ($search_value, $request) {
                    /** @var Builder $dbQuery */
                    $preparedQ = '%' . $search_value . '%';
                    $num = 0;
                    foreach ([
                        'quality',
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
        $dbQuery->orderBy('quality', 'ASC');
        return datatables()->of($dbQuery)->toJson();
    }

    public function QualityDelete(Request $request)
    {
        try {
            $city = Quality::find($request->delete_quality_id);
            $city->delete();
            Session::flash('status', 'success');
            Session::flash('message', 'Quality Successfully Deleted.');
        } catch (Exception $city) {
            Session::flash('status', 'danger');
            Session::flash('message', "Quality not allow to delete. Because it's Used.");
            return redirect()->back();
        }
        return redirect()->back();
    }
}
