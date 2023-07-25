<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;
use App\PurchaseOrder;
use App\RehiringOrder;
use App\OtherCost;
use App\OtherIncome;
use App\SalesOrder;


class PurchaseOrderController extends Controller
{
    public function index(){
        $purchaseorders = PurchaseOrder::all();

        // $purchaseorder = DB::table('purchase_orders')
        //             ->join('sales_orders','sales_orders.id','=','purchase_orders.id_sales_order')
        //             ->select('purchase_orders.*','sales_orders.agreement_no')
        //             ->get();

        if(count($purchaseorders) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $purchaseorders
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function indexAll(){
        $purchaseorders = PurchaseOrder::all();

        if(count($purchaseorders) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $purchaseorders
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function showVehicleNumberinSales(){
        
        $purchaseorder = DB::table('purchase_orders')
                     ->select('purchase_orders.vehicle_registration')
                     ->where('status_next_step','Available')
                     ->get();

        if(count($purchaseorder) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $purchaseorder
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);

    }

    public function show($id){
        $purchaseorder = PurchaseOrder::find($id);

        if(!is_null($purchaseorder)){
            return response([
                'message' => 'Retrieve Purchase Order Success',
                'data' => $purchaseorder
            ],200);
        }

        return response([
            'message' => 'Purchase Order Not Found',
            'data' => null
        ],400);
    }

    //
     public function showVehicle(){
         $purchaseorder = DB::table('purchase_orders')
                         ->join('sales_orders','sales_orders.id','=','purchase_orders.id_sales_order')
                         ->select('purchase_orders.*','sales_orders.agreement_no')
                         //->whereRaw('vehicle_registration = "'.$vehicle_number.'"')
                         ->get();
             if(count($purchaseorder) > 0){
             return response([
                 'message' => 'Retrieve All Success',
                 'data' => $purchaseorder
             ],200);
         }
              
         return response([
             'message' => 'Empty',
             'data' => null
         ],400);
     }

    //
    public function listVehicleById($id){
        $rehiringByPurchaseId = RehiringOrder::whereRaw('id_purchase_order = '.$id)->first();
        
        if($rehiringByPurchaseId != null){
            $purchaseorder = SalesOrder::join('purchase_orders','purchase_orders.id','=','sales_orders.id_purchase_order')
                        ->join('rehiring_orders','rehiring_orders.id_purchase_order','=','purchase_orders.id')
                        ->whereRaw('purchase_orders.id = '.$id)
                        ->first();
        }
        else{
            $purchaseorder = SalesOrder::join('purchase_orders','purchase_orders.id','=','sales_orders.id_purchase_order')
                        ->whereRaw('purchase_orders.id = '.$id)
                        ->first();
        }
        if($purchaseorder != null){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $purchaseorder
            ],200);
        }
                
        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function listVehicleInVehicleCard($id){
         $purchaseorder = DB::table('sales_orders')
                     ->join('purchase_orders','purchase_orders.id','=','sales_orders.id_purchase_order')
                     ->join('rehiring_orders','rehiring_orders.id_purchase_order','=','purchase_orders.id')
                     ->select('purchase_orders.*','sales_orders.*','rehiring_orders.next_step','rehiring_orders.vehicle_return_date','rehiring_orders.sold_price')
                     ->whereRaw('sales_orders.id_purchase_order = '.$id)
                     ->get();

        if(count($purchaseorder) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $purchaseorder
            ],200);
        }
            
        return response([
            'message' => 'Empty',
            'data' => null
        ],400);

    }

    public function showVehicleNumber(){
        $rehiringorder = RehiringOrder::select('rehiring_orders.id_purchase_order')->get();
        
        $purchaseorder = DB::table('purchase_orders')
                    ->select('id','vehicle_registration')
                    ->whereNotIn('id',$rehiringorder)
                    ->whereOr()
                    ->get();

        if(count($purchaseorder) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $purchaseorder
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function showVehicleNumberInOtherCost(){
        $othercost = OtherCost::select('other_costs.id_purchase_order')->get();
        
        $purchaseorder = DB::table('purchase_orders')
                    ->select('id','vehicle_registration')
                    ->whereNotIn('id',$othercost)
                    ->whereOr()
                    ->get();

        if(count($purchaseorder) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $purchaseorder
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function showVehicleNumberInOtherIncome(){
        $otherincome = OtherIncome::select('other_incomes.id_purchase_order')->get();
        
        $purchaseorder = DB::table('purchase_orders')
                    ->select('id','vehicle_registration')
                    ->whereNotIn('id',$otherincome)
                    ->whereOr()
                    ->get();

        if(count($purchaseorder) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $purchaseorder
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    //kalau sales yang muncul 2, tapi purchase yang ga memiliki sales ga muncul. Kalau purchase, sales yang double ga muncul
    public function compilationDB(){

        $purchaseorder = DB::table('purchase_orders')
                        ->leftJoin('sales_orders','purchase_orders.id','=','sales_orders.id_purchase_order')
                        ->leftJoin('other_incomes','purchase_orders.id','=','sales_orders.id_purchase_order')
                        ->leftJoin('other_costs','purchase_orders.id','=','other_costs.id_purchase_order')
                        ->leftJoin('rehiring_orders','purchase_orders.id','=','rehiring_orders.id_purchase_order')
                        ->select('*')
                        ->groupBy('agreement_no')
                        ->get();
        
        if(count($purchaseorder) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $purchaseorder
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }


    // public function showAgreementNumber($id){
    //     $purchaseorder = DB::table('purchase_orders')
    //                 ->join('sales_orders','sales_orders.id','=','purchase_orders.id_sales_order')
    //                 ->select('purchase_orders.*','sales_orders.agreement_no')
    //                 ->get();

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

    

    //  public function showVehicleRehiringOrder($id){
    //      $purchaseorder = DB::table('purchase_orders')
    //                      ->join('sales_orders','sales_orders.id','=','purchase_orders.id_sales_order')
    //                      ->join('rehiring_orders','rehiring_orders.id','=','purchase_orders.id_rehiring_order')
    //                      ->select('purchase_orders.*','sales_orders.agreement_no','rehiring_orders.next_step')
    //                      ->whereRaw('purchase_orders.id = "'.$id.'"')
    //                      ->get();

    //      if(count($purchaseorder) > 0){
    //          return response([
    //              'message' => 'Retrieve All Success',
    //              'data' => $purchaseorder
    //          ],200);
    //      }
                
    //      return response([
    //          'message' => 'Empty',
    //          'data' => null
    //      ],400);
    //  }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_sales_order'                => 'nullable',
            'purchase_method'               => 'required|in:Hire Purchase,Cash,Rent/Return',
            'vehicle_registration'          => 'required',
            'hp_finance_provider'           => 'required',
            'hire_purchase_starting_date'   => 'required|date_format:Y-m-d',
            'hp_interest_per_annum'         => 'required',
            'hp_deposit_amount'             => 'required',
            'hp_term'                       => 'required',
            'documentation_fees_pu'         => 'required',
            'final_fees'                    => 'required',
            'other_fees'                    => 'required',
            'price_otr'                     => 'required',
            'monthly_payment'               => 'required',
            'final_payment'                 => 'required',
            'hp_interest_type'              => 'required|in:Variable,Non HP,Fixed',
            'financing_amount'              => 'nullable',
            'regular_monthly_payment'       => 'nullable',
            'status_next_step'              => 'nullable'
        ]);

        if($validate->fails())
            return response (['message' => $validate->errors()],400);

        $checkPurchaseOrderExist = PurchaseOrder::whereRaw('vehicle_registration = "'.$request->vehicle_registration.'" and status_next_step in ("Available", "Hired")')->get();
        if(count($checkPurchaseOrderExist) > 0){
            return response (['message' => 'Vehicle number cannot process'],400);
        }

        $purchaseorder = PurchaseOrder::create($storeData);

        $purchaseorder->status_next_step = 'Available';
        
        //fo003
         if($purchaseorder->purchase_method != 'Hire Purchase' && $purchaseorder->purchase_method != 'Rent/Return') {
             $purchaseorder->financing_amount = 0;
             $purchaseorder->save();
         } else {
             if($purchaseorder->price_otr >= $purchaseorder->deposit) {
                $purchaseorder->financing_amount = $purchaseorder->price_otr - $purchaseorder->hp_deposit_amount;
                $purchaseorder->save();
             } else if ($purchaseorder->deposit > $purchaseorder->price_otr){
                $purchaseorder->financing_amount = ($purchaseorder->hp_deposit_amount - $purchaseorder->price_otr) * 1 ;
             }
         }

          //fo004
          if($purchaseorder->purchase_method != 'Hire Purchase' && $purchaseorder->purchase_method != 'Rent/Return') {
              $purchaseorder->regular_monthly_payment = 0;
              $purchaseorder->save();
          } else {
              $purchaseorder->regular_monthly_payment = ($purchaseorder->monthly_payment + ($purchaseorder->financing_amount * $purchaseorder->hp_interest_per_annum) / 12);
              $purchaseorder->save();
          }

        return response([
            'message' => 'Add Purchase Order Success',
            'data' => $purchaseorder,
        ],200);
    }

    public function destroy($id){
        $purchaseorder = PurchaseOrder::find($id);

        if(is_null($purchaseorder)){
            return response([
                'message' => 'Purchase Order Not Found',
                'data' => null
            ],404);
        }

        if($purchaseorder->delete()){
            return response([
                'message' => 'Delete Purchase Order Success',
                'data' => $purchaseorder,
            ],200);
        }
        
        return response([
            'message' => 'Delete Purchase Order Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $purchaseorder = PurchaseOrder::find($id);
        if(is_null($purchaseorder)){
            return response([
                'message' => 'Purchase Order Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'id_sales_order'                => 'nullable',
            'purchase_method'               => 'required|in:Hire Purchase,Cash,Rent/Return',
            'vehicle_registration'          => 'required',
            'hp_finance_provider'           => 'required',
            'hire_purchase_starting_date'   => 'required|date_format:Y-m-d',
            'hp_interest_per_annum'         => 'required',
            'hp_deposit_amount'             => 'required',
            'hp_term'                       => 'required',
            'documentation_fees_pu'         => 'required',
            'final_fees'                    => 'required',
            'other_fees'                    => 'required',
            'price_otr'                     => 'required',
            'monthly_payment'               => 'required',
            'final_payment'                 => 'required',
            'hp_interest_type'              => 'required|in:Variable,Non HP,Fixed',
            'financing_amount'              => 'nullable',
            'regular_monthly_payment'       => 'nullable',
            'status_next_step'              => 'nullable'
        ]);

        if($validate->fails())
        return response(['message' => $validate->errors()],400);

        //$purchaseorder->id_sales_order                = $updateData['id_sales_order'];
        $purchaseorder->purchase_method               = $updateData['purchase_method'];
        $purchaseorder->vehicle_registration          = $updateData['vehicle_registration'];
        $purchaseorder->hp_finance_provider           = $updateData['hp_finance_provider'];
        $purchaseorder->hire_purchase_starting_date   = $updateData['hire_purchase_starting_date'];
        $purchaseorder->hp_interest_per_annum         = $updateData['hp_interest_per_annum'];
        $purchaseorder->hp_deposit_amount             = $updateData['hp_deposit_amount'];
        $purchaseorder->hp_term                       = $updateData['hp_term'];
        $purchaseorder->documentation_fees_pu         = $updateData['documentation_fees_pu'];
        $purchaseorder->final_fees                    = $updateData['final_fees'];
        $purchaseorder->other_fees                    = $updateData['other_fees'];
        $purchaseorder->price_otr                     = $updateData['price_otr'];
        $purchaseorder->monthly_payment               = $updateData['monthly_payment'];
        $purchaseorder->final_payment                 = $updateData['final_payment'];
        $purchaseorder->hp_interest_type              = $updateData['hp_interest_type'];
        
        //fo003
        if($purchaseorder->purchase_method != 'Hire Purchase' && $purchaseorder->purchase_method != 'Rent/Return') {
            $purchaseorder->financing_amount = 0;
            $purchaseorder->save();
        } else {
            if($purchaseorder->price_otr >= $purchaseorder->deposit) {
               $purchaseorder->financing_amount = $purchaseorder->price_otr - $purchaseorder->hp_deposit_amount;
               $purchaseorder->save();
            } else if ($purchaseorder->deposit > $purchaseorder->price_otr){
               $purchaseorder->financing_amount = ($purchaseorder->hp_deposit_amount - $purchaseorder->price_otr) * 1 ;
            }
        }

        //fo004
        if($purchaseorder->purchase_method != 'Hire Purchase' && $purchaseorder->purchase_method != 'Rent/Return') {
            $purchaseorder->regular_monthly_payment = 0;
            $purchaseorder->save();
        } else {
            $purchaseorder->regular_monthly_payment = ($purchaseorder->monthly_payment + ($purchaseorder->financing_amount * $purchaseorder->hp_interest_per_annum) / 12);
            $purchaseorder->save();
        }

        if($purchaseorder->save()){
            return response([
                'message' => 'Update Purchase Order Success',
                'data' => $purchaseorder,
            ],200);
        }

        return response([
            'message' => 'Update Purchase Order Failed',
            'data' => null
        ],400);
    }
    
}
