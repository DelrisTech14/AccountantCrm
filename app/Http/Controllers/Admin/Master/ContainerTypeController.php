<?php

namespace App\Http\Controllers\Admin\Master;

use App\ContainerType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use DB;
use Exception;
use PhpParser\Node\Stmt\TryCatch;
use Validator;

class ContainerTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = array();
        return view('admin.master.containerType.index', $data);
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
            'container_type_name' => 'required|unique:container_type,container_type_name,' . $request->id,
        ];
        $validator = Validator::make($_POST, $rules);
        if ($validator->passes()) {
            if (!empty($request->id)) {
                $container_type = ContainerType::where('id', $request->id)->first();
                $container_type->updated_by = auth()->user()->id;
            } else {
                $container_type = new ContainerType();
                $container_type->created_by = auth()->user()->id;
                $container_type->updated_by = auth()->user()->id;
            }
            $container_type->container_type_name = !empty($request->container_type_name) ? $request->container_type_name : NULL;
            $container_type->save();
            if ($container_type->id) {
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
        $return['container_type_data'] = ContainerType::find($id);
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
    
    public function getContainerTypeDatatable(Request $request)
    {
        $data = $request->all();
        $search_value = trim($data['search']['value']);
        $user = auth()->user();
        $cityQuery = ContainerType::select(DB::raw('*'))
            ->when($search_value, function ($cityQuery) use ($search_value, $request) {
                return $cityQuery->where(function ($cityQuery) use ($search_value, $request) {
                    /** @var Builder $cityQuery */
                    $preparedQ = '%' . $search_value . '%';
                    $num = 0;
                    foreach ([
                        'container_type_name',
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
        $cityQuery->orderBy('container_type_name', 'ASC');
        return datatables()->of($cityQuery)->toJson();
    }
    public function ContainerTypeDelete(Request $request)
    {
        try {
            $city = ContainerType::find($request->delete_container_type_id);
            $city->delete();
            Session::flash('status', 'success');
            Session::flash('message', 'ContainerType Successfully Deleted.');
        } catch (Exception $city) {
            Session::flash('status', 'danger');
            Session::flash('message', "ContainerType not allow to delete. Because it's Used.");
            return redirect()->back();
        }
        return redirect()->back();
    }
}
