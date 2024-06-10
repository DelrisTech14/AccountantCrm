<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\AccountGroups;
class AccountGroupController extends Controller
{
	
   public function index()
   {
	
        $data = array();
       // $data['accountgroup'] = AccountGroups::all();
        
        return view('admin.master.accountgroup.index', $data);
    }	
    public function GetAccountGroupDatatable(Request $request)
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
        $cityQuery->orderBy('name', 'ASC');
        return datatables()->of($cityQuery)->toJson();
    }
    //
}
