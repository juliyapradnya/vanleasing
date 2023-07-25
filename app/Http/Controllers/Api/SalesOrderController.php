<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;
use App\SalesOrder;
use App\PurchaseOrder;
use App\RehiringOrder;
use Haruncpi\LaravelIdGenerator\IdGenerator;


class SalesOrderController extends Controller
{
    public function index(){
        //$salesorders = SalesOrder::all();

         $salesorders = DB::table('sales_orders')
                     ->join('purchase_orders','purchase_orders.id','=','sales_orders.id_purchase_order')
                     ->select('sales_orders.*','purchase_orders.vehicle_registration')
                     ->get();

        if(count($salesorders) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $salesorders
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function showAgreementNumber(){
        $purchaseorder = PurchaseOrder::select('purchase_orders.id_sales_order')->get();
        
        $salesorder = DB::table('sales_orders')
                    ->select('id','agreement_no')
                    ->whereNotIn('id',$purchaseorder)
                    ->whereOr()
                    ->get();

        if(count($salesorder) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $salesorder
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function showAgreementNumberInRehiring(){
        $rehiringorder = RehiringOrder::select('rehiring_orders.id_sales_order')->get();
        
        $salesorder = DB::table('sales_orders')
                    ->select('id','agreement_no')
                    ->whereNotIn('id',$rehiringorder)
                    ->whereOr()
                    ->get();

        if(count($salesorder) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $salesorder
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    // public function showVehicle(){
    //     $purchaseorder = DB::table('sales_orders')
    //                     ->join('purchase_orders','purchase_orders.id','=','sales_orders.id_purchase_order')
    //                     ->select('sales_orders.*','purchase_orders.vehicle_registration')
    //                     //->whereRaw('vehicle_registration = "'.$vehicle_number.'"')
    //                     ->get();

    //     if(count($purchaseorder) > 0){
    //         return response([
    //             'message' => 'Retrieve All Success',
    //             'data' => $purchaseorder
    //         ],200);
    //     }
                
    //     return response([
    //         'message' => 'Empty',
    //         'data' => null
    //     ],400);
    // }

    public function show($id){
        $salesorder = SalesOrder::find($id);

        if(!is_null($salesorder)){
            return response([
                'message' => 'Retrieve Sales Order Success',
                'data' => $salesorder
            ],200);
        }

        return response([
            'message' => 'Sales Order Not Found',
            'data' => null
        ],400);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_purchase_order'     => 'required',
            'type'                  => 'required|in:Contract Hire (Unregulated),Hire (Unregulated)',
            'agreement_no'          => 'nullable',
            'cust_name'             => 'required',
            'sales_person'          => 'required',
            'contract_start_date'   => 'required|date_format:Y-m-d',
            'vehicle_manufacturer'  => 'required',
            'vehicle_model'         => 'required',
            'vehicle_variant'       => 'required',
            'basic_list_price'      => 'required',
            'residual_value'        => 'required',
            'annual_mileage'        => 'required',
            'term_months'           => 'required',
            'initial_rental'        => 'required',
            'documentation_fees'    => 'required',
            'monthly_rental'        => 'required',
            'other_income'          => 'required',
            'margin_term'           => 'nullable',
            'total_income'          => 'nullable',
            'next_step_status_sales'  => 'nullable'
        ]);

        if($validate->fails())
            return response (['message' => $validate->errors()],400);

        $salesorder = SalesOrder::create($storeData);
        $purchaseorder = PurchaseOrder::find($salesorder->id_purchase_order);

        $salesorder->next_step_status_sales = 'Hired';

        //fo001
         if($salesorder->term_months != null) {
             $salesorder->margin_term = $salesorder->term_months;
             $salesorder->save();
         }

        $salesorder->agreement_no = IdGenerator::generate(['table' => 'sales_orders','field'=>'agreement_no', 'length' => 7, 'prefix' =>'SO-']);
        //output: P00001
        $salesorder->save();

        $purchaseorder->status_next_step = 'Hired';
        $purchaseorder->save();

        return response([             
            'message' => 'Add Sales Order Success',
            'data' => $salesorder,
        ],200);
    }

    public function destroy($id){
        $salesorder = SalesOrder::find($id);

        if(is_null($salesorder)){
            return response([
                'message' => 'Sales Order Not Found',
                'data' => null
            ],404);
        }

        if($salesorder->delete()){
            return response([
                'message' => 'Delete Sales Order Success',
                'data' => $salesorder,
            ],200);
        }
        
        return response([
            'message' => 'Delete Sales Order Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $salesorder = SalesOrder::find($id);
        if(is_null($salesorder)){
            return response([
                'message' => 'Sales Order Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'id_purchase_order'     => 'required',
            'type'                  => 'required|in:Contract Hire (Unregulated),Hire (Unregulated)',
            'agreement_no'          => 'nullable',
            'cust_name'             => 'required',
            'sales_person'          => 'required',
            'contract_start_date'   => 'required|date_format:Y-m-d',
            'vehicle_manufacturer'  => 'required',
            'vehicle_model'         => 'required',
            'vehicle_variant'       => 'required',
            'basic_list_price'      => 'required',
            'residual_value'        => 'required',
            'annual_mileage'        => 'required',
            'term_months'           => 'required',
            'initial_rental'        => 'required',
            'documentation_fees'    => 'required',
            'monthly_rental'        => 'required',
            'other_income'          => 'required',
            'margin_term'           => 'nullable',
            'total_income'          => 'nullable',
            'next_step_status_sales'  => 'nullable'
        ]);

        if($validate->fails())
        return response(['message' => $validate->errors()],400);

        $salesorder->id_purchase_order     = $updateData['id_purchase_order'];
        $salesorder->type                  = $updateData['type'];
        $salesorder->agreement_no          = $updateData['agreement_no'];
        $salesorder->cust_name             = $updateData['cust_name'];
        $salesorder->sales_person          = $updateData['sales_person'];
        $salesorder->contract_start_date   = $updateData['contract_start_date'];
        $salesorder->vehicle_manufacturer  = $updateData['vehicle_manufacturer'];
        $salesorder->vehicle_model         = $updateData['vehicle_model'];
        $salesorder->vehicle_variant       = $updateData['vehicle_variant'];
        $salesorder->basic_list_price      = $updateData['basic_list_price'];
        $salesorder->residual_value        = $updateData['residual_value'];
        $salesorder->annual_mileage        = $updateData['annual_mileage'];
        $salesorder->term_months           = $updateData['term_months'];
        $salesorder->initial_rental        = $updateData['initial_rental'];
        $salesorder->documentation_fees    = $updateData['documentation_fees'];
        $salesorder->monthly_rental        = $updateData['monthly_rental'];
        $salesorder->other_income          = $updateData['other_income'];
        $salesorder->next_step_status_sales          = $updateData['next_step_status_sales'];
        

        //fo001
        if($salesorder->term_months != null) {
            $salesorder->margin_term = $salesorder->term_months;
            $salesorder->save();
        }

        if($salesorder->save()){
            return response([
                'message' => 'Update Sales Order Success',
                'data' => $salesorder,
            ],200);
        }

        return response([
            'message' => 'Update Sales Order Failed',
            'data' => null
        ],400);
    }
    
}
