<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\FreightImport;
use App\Imports\PortImport;
use App\Imports\QualityImport;
use App\Imports\CityImport;
use App\Imports\ExpensesNameImport;
use App\Imports\ExpenseImport;
use App\Imports\BankImport;
use App\Imports\ContainerTypeImport;
use App\Imports\CurrencyImport;
use App\Imports\PaymentTermsImport;
use App\Imports\QuantityInImport;
use App\Imports\LabImport;
use App\Imports\StateImport;
use App\Imports\ToleranceImport;
use App\Imports\PackingImport;
use App\Imports\FumigationDosageImport;
use App\Imports\CommoditiesImport;
use App\Imports\CommodityContainerTypeWiseQtyImport;
use DB;
//use App\Item;

class ImportDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = array();
        return view('admin.importData.index', $data);
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
        $return['not_inserted_data'] = array();
        $return['master_inserted_data'] = array();
        $return['import_type'] = '';
//        echo "<pre>"; print_r($request->import_radio); exit;
        try {
            if($request->import_radio == 'import_freight_data'){
                Excel::import(new FreightImport, $request->file('import_file')->store('temp'));
            } else if($request->import_radio == 'import_port_data'){
                Excel::import(new PortImport, $request->file('import_file')->store('temp'));
            } else if($request->import_radio == 'import_quality_data'){
                Excel::import(new QualityImport, $request->file('import_file')->store('temp'));
            } else if($request->import_radio == 'import_city_data'){
                Excel::import(new CityImport, $request->file('import_file')->store('temp'));
            } else if($request->import_radio == 'import_expenses_name_data'){
                Excel::import(new ExpensesNameImport, $request->file('import_file')->store('temp'));
            } else if($request->import_radio == 'import_bank_data'){
                Excel::import(new BankImport, $request->file('import_file')->store('temp'));
            } else if($request->import_radio == 'import_container_types_data'){
                Excel::import(new ContainerTypeImport, $request->file('import_file')->store('temp'));
            } else if($request->import_radio == 'import_currency_data'){
                Excel::import(new CurrencyImport, $request->file('import_file')->store('temp'));
            } else if($request->import_radio == 'import_payment_terms_data'){
                Excel::import(new PaymentTermsImport, $request->file('import_file')->store('temp'));
            } else if($request->import_radio == 'import_quantity_in_data'){
                Excel::import(new QuantityInImport, $request->file('import_file')->store('temp'));
            } else if($request->import_radio == 'import_lab_data'){
                Excel::import(new LabImport, $request->file('import_file')->store('temp'));
            } else if($request->import_radio == 'import_state_data'){
                Excel::import(new StateImport, $request->file('import_file')->store('temp'));
            } else if($request->import_radio == 'import_tolerance_data'){
                Excel::import(new ToleranceImport, $request->file('import_file')->store('temp'));
            } else if($request->import_radio == 'import_packing_data'){
                Excel::import(new PackingImport, $request->file('import_file')->store('temp'));
            } else if($request->import_radio == 'import_fumigation_and_fumigation_dosage_data'){
                Excel::import(new FumigationDosageImport, $request->file('import_file')->store('temp'));
            } else if($request->import_radio == 'import_expense_data'){
                $import = new ExpenseImport;
                Excel::import($import, $request->file('import_file')->store('temp'));
                $return['not_inserted_data'] = $import->not_inserted_data;
                $return['master_inserted_data'] = $import->master_inserted_data;
                $return['import_type'] = 'import_expense_data';
            } else if($request->import_radio == 'import_commodities_data'){
                Excel::import(new CommoditiesImport, $request->file('import_file')->store('temp'));
            } else if($request->import_radio == 'import_commodity_container_type_wise_qty_data'){
                Excel::import(new CommodityContainerTypeWiseQtyImport, $request->file('import_file')->store('temp'));
            }
//            return back();
            $return['success'] = "Added";
        } catch (\Exception $e) {
//        } catch (\Illuminate\Database\QueryException $exception) {
            // You can check get the details of the error using `errorInfo`:
//            $errorInfo = $exception->errorInfo;

            // Return the response to the client..
            $return['error'] = "error";
//            $return['errorInfo'] = $errorInfo;
        }
//        echo "<pre>"; print_r($return); exit;
        echo json_encode($return);
        exit;
    }
}
