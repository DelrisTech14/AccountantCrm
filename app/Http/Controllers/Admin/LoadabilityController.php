<?php

namespace App\Http\Controllers\Admin;

use App\Commodity;
use App\ContainerType;
use App\Http\Controllers\Controller;
use App\Loadability;
use App\Quality;
use Illuminate\Http\Request;

class LoadabilityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loadabilities = Loadability::with('commodity')->get();
        $qualities= Quality::all();
        $commodity= Commodity::all();
        $container_type= ContainerType::all();
        $mode='create';
       

        return view('admin.loadability.index',compact('qualities','commodity','container_type','loadabilities','mode'));
    }
    public function store(Request $request)
    {

        $request->validate([
            'loadbility'=>'required',
            'commodity_id'=>'required',
            'quality_id'=>'required',
            'container_type_id'=>'required',

        ]);
        $loadabilities= new Loadability();


        $loadabilities->loadbility = $request->loadbility;
        $loadabilities->commodity_id = $request->commodity_id;
        $loadabilities->quality_id = $request->quality_id;
        $loadabilities->container_type_id = $request->container_type_id;

        $loadabilities->save();

        return redirect()->back()->with('message','New Loadability Created');
    }

    public function edit($id)
    {
        $loadabilities = Loadability::with('commodity')->get();
        $qualities= Quality::all();
        $commodity= Commodity::all();
        $container_type= ContainerType::all();
        $loadabilityedit= Loadability::find($id);
        $mode='edit';
        return view('admin.loadability.index',compact('loadabilityedit','qualities','commodity','container_type','loadabilities', 'mode'));

    }
    public function update( Request $request ,$id)
    {

        $request->validate([
            'loadbility'=>'required',
            'commodity_id'=>'required',
            'quality_id'=>'required',
            'container_type_id'=>'required',

        ]);

       
        $loadabilityedit= Loadability::find($id);

        $loadabilityedit->loadbility = $request->loadbility;
        $loadabilityedit->commodity_id = $request->commodity_id;
        $loadabilityedit->quality_id = $request->quality_id;
        $loadabilityedit->container_type_id = $request->container_type_id;

        $loadabilityedit->save();

        return redirect()->route('index')->with('message','Update Successfully');

    }

    public function destroy($id){
        $loadabilitydelete = Loadability::find($id);
        
        $loadabilitydelete->delete();

        return redirect()->route('index')->with('message','Delete Successfully');

    }

    public function loadAbilities($commodity_id)
    {
        // dd($commodity_id);
        return  Quality::where('commodity_id',$commodity_id)->get();
        // return Quality::where('commodity_id',$request->commodity_id)->get();

        // return response()->json($data);

    }
    // 31.1.2023
    public function get_loadability(Request $request, $id){
       $row = Loadability::where('container_type_id', $id)
                        ->where('commodity_id', $request->commodity_id)
                        ->first();
        return response()->json(['status' => 1, 'data' => $row]);                
       
    }


}
