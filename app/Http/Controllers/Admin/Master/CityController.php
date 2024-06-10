<?php

namespace App\Http\Controllers\Admin\Master;

use App\City;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use DB;
use Exception;
use PhpParser\Node\Stmt\TryCatch;
use Validator;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = array();
        return view('admin.master.city.index', $data);
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
        //
        $return = array();
        $rules = [
            'city_name' => 'required|unique:city,city_name,' . $request->id,
        ];
        $validator = Validator::make($_POST, $rules);
        if ($validator->passes()) {
            if (!empty($request->id)) {
                $city = City::where('id', $request->id)->first();
                $city->updated_by = auth()->user()->id;
            } else {
                $city = new City();
                $city->created_by = auth()->user()->id;
                $city->updated_by = auth()->user()->id;
            }
            $city->city_name = !empty($request->city_name) ? $request->city_name : NULL;
            $city->save();
            if ($city->id) {
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
        //
        $return = array();
        $return['city_data'] = City::find($id);
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
    public function getCityDatatable(Request $request)
    {
        $data = $request->all();
        $search_value = trim($data['search']['value']);
        $user = auth()->user();
        $cityQuery = City::select(DB::raw('*'))
            ->when($search_value, function ($cityQuery) use ($search_value, $request) {
                return $cityQuery->where(function ($cityQuery) use ($search_value, $request) {
                    /** @var Builder $cityQuery */
                    $preparedQ = '%' . $search_value . '%';
                    $num = 0;
                    foreach ([
                        'city_name',
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
        $cityQuery->orderBy('city_name', 'ASC');
        return datatables()->of($cityQuery)->toJson();
    }
    public function CityDelete(Request $request)
    {
        try {
            $city = City::find($request->delete_city_id);
            $city->delete();
            Session::flash('status', 'success');
            Session::flash('message', 'City Successfully Deleted.');
        } catch (Exception $city) {
            Session::flash('status', 'danger');
            Session::flash('message', "City not allow to delete. Because it's Used.");
            return redirect()->back();
        }
        return redirect()->back();
    }
}
