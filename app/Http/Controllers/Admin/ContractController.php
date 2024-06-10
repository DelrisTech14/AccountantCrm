<?php

namespace App\Http\Controllers\Admin;

use App\City;
use App\Contracts;
use App\Bank;
use App\Accounts;
use App\State;
use App\Commodity;
use App\Unit;
use App\Currency;
use App\ContractItem;
use App\Tolerance;
use App\ContractDestination;
use App\Port;
use App\Country;
use App\Quality;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use DB;
use Exception;
use PhpParser\Node\Stmt\TryCatch;
use Validator;
use PDF;

class ContractController extends Controller
{

    public function __construct()
    {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (request()->ajax()) {
            $data = Contracts::select('id as DT_RowId', 'contracts.*')->get();
            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $button = '<div class="tb-icon-wrap">';
                    $button .= '<a class="btn btn-success btn-xs action-btn"
                                    href="hotel/' . $data->id . '/edit"><i class="fa fa-edit"></i></a>';
                    $button .= '<a class="btn btn-info btn-xs action-btn"
                                    href="hotel/' . $data->id . '"><i class="fa fa-eye"></i></a>';

                    $button .= '<button class="delete btn btn-danger btn-xs action-btn" data-id="' . $data->id . '"><i class="fa fa-trash-o"></i></button></div>';
                    return $button;
                })->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.contract.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $banks = Bank::all();
        $sellers = Accounts::select('accounts.*'
            )->join('account_groups','account_groups.id', 'accounts.group_id'
            )->where('account_groups.name','like', 'Seller')->get();
        $buyers = Accounts::select('accounts.*'
            )->join('account_groups','account_groups.id', 'accounts.group_id'
            )->where('account_groups.name','like', 'Buyer')->get();
        $cities = City::all();
        $states = State::all();
        $commodities = Commodity::all();
        $units = Unit::all();
        $currencies = Currency::all();
        $ports = Port::all();
        $tolerances = Tolerance::all();
        $countries = Country::all();
        $qualities = Quality::all();
        $shipments = [];
        $surveyors = Accounts::select('accounts.name', 'accounts.id'
            )->join('account_groups','account_groups.id', 'accounts.group_id'
            )->where('account_groups.name','like', 'Surveyor')->get();;
        
        return view('admin.contract.create',
            compact(
                'banks', 'sellers', 'buyers', 'cities', 'states', 'commodities',
                'units', 'currencies', 'ports', 'shipments', 'surveyors',
                'tolerances','countries', 'qualities'
            )
        );
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
        try {
            // echo "<pre>";
            // print_r($request->all());
            // exit;
            $max = Contracts::max('id')+1;
            $con_no = date('n').$max.'.'.date('y') ;
            $contract = new Contracts();
            $contract->con_no = $con_no;
            $contract->date = date('Y-m-d', strtotime($request->date));
            $contract->seller_id = $request->seller_id;
            $contract->seller_name = $request->seller_name;
            $contract->seller_address = $request->seller_address;
            $contract->seller_city = $request->city;
            $contract->seller_pincode = $request->pincode;
            $contract->seller_state = $request->state;
            $contract->seller_country = $request->country;
            $contract->buyer_id = $request->buyer_id;
            $contract->buyer_name = $request->buyer_name;
            $contract->buyer_address = $request->buyer_address;
            $contract->buyer_city = $request->buyer_city;
            $contract->buyer_pincode = $request->buyer_pincode;
            $contract->buyer_state = $request->buyer_state;
            $contract->buyer_country = $request->buyer_country;
            $contract->payment_terms = $request->payment_terms;
            $contract->bank_id = $request->bank;
            $contract->bank_detail = "Bank Detail";
            $contract->usa_ref_id = $request->usa_reference_bank;
            $contract->usa_reference_bank = "Ref Bank Detail";
            $contract->surveyor_id = $request->surveyor;
            $contract->surveyor = $request->surveyor_name;
            $contract->save();
            $id = $contract->id;

            if (isset($request->item_id) && count($request->item_id) > 0) {

                foreach($request->item_id as $key => $item) {
                    $contractItem = new ContractItem();
                    $contractItem->contract_id = $id;
                    $contractItem->item_id = $item;
                    $contractItem->qty = $request->quantity[$key];
                    $contractItem->quality = $request->quality[$key];
                    $contractItem->quantity_unit = $request->unit[$key];
                    $contractItem->price = $request->price[$key];
                    $contractItem->tolerance_id = $request->tolerance_id[$key];
                    $contractItem->currency_id = $request->currency[$key];
                    $contractItem->cif_fob = $request->cif_fob[$key];
                    $contractItem->port = $request->port[$key];
                    $contractItem->packing = $request->packing[$key];
                    $contractItem->shipping_marks = $request->shipping_marks[$key];
                    // $contractItem->shipment_id = 1;
                    $contractItem->shipment = $request->shipment[$key];
                    $contractItem->port_of_loading = $request->port_of_loading[$key];
                    $contractItem->port_of_destination_id = $request->port_of_destination[$key];;
                    $contractItem->port_of_destination = $request->port_of_destination[$key];;
                    $contractItem->shipment_date = date('Y-m-d', strtotime($request->shipment_date[$key]));

                    $contractItem->save();
                }
            }

            return redirect('/admin/contracts');

        } catch (\Exception $e) {
            //throw $th;
            print_r($e->getMessage());
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Contracts  $contracts
     * @param  \App\ContractItem  $contractItem
     * @return \Illuminate\Http\Response
     */
    public function show(Contracts $contracts, ContractItem $contractItem)
    {
        //
    }

    /**
     * Throw the PDF output to display the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function print($id)
    {
        try {
            $data = [];
            $pdf = PDF::loadView('admin.contract.pdf', $data);
            $pdf->setOptions([
                'images' => true
            ]);
            $pdf->setPaper('a4', 'portrait');

            return $pdf->download("$id-contract.pdf");
            // return view('admin.contract.pdf');

        } catch (\Exception $e) {
            print_r($e->getMessage());
        }

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
}
