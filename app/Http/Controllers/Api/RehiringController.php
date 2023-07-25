<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;
use App\RehiringOrder;
use App\PurchaseOrder;
use App\SalesOrder;
use App\OtherIncome;
use Haruncpi\LaravelIdGenerator\IdGenerator;


class RehiringController extends Controller
{
    public function index(){
        //$rehiringorders = RehiringOrder::all();
        $rehiringorders = DB::table('rehiring_orders')
                    ->join('sales_orders','sales_orders.id','=','rehiring_orders.id_sales_order')
                    ->join('purchase_orders','purchase_orders.id','=','rehiring_orders.id_purchase_order')
                    ->select('rehiring_orders.*','sales_orders.agreement_no','purchase_orders.vehicle_registration')
                    ->whereRaw('next_step = "Rehiring"')
                    ->get();

        if(count($rehiringorders) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $rehiringorders
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);

    }

    //tampil dialog di serach, dimana vehicle regis number masuk di database rehiring
    //  public function showVehicleRehiringOrder(){
    //      $rehiringorder = DB::table('rehiring_orders')
    //                      ->join('sales_orders','sales_orders.id','=','rehiring_orders.id_sales_order')
    //                      ->join('purchase_orders','purchase_orders.id','=','rehiring_orders.id_purchase_order')
    //                      ->select('rehiring_orders.*','sales_orders.agreement_no','purchase_orders.vehicle_registration')
    //                      //->whereRaw('rehiring_orders.id = "'.$id.'"')
    //                      ->get();

    //      if(count($rehiringorder) > 0){
    //          return response([
    //              'message' => 'Retrieve All Success',
    //              'data' => $rehiringorder
    //          ],200);
    //      }
                
    //      return response([
    //          'message' => 'Empty',
    //          'data' => null
    //      ],400);
    //  }

    public function showVehicleSold(){
        //$rehiringorders = RehiringOrder::all();
        $rehiringorders = DB::table('rehiring_orders')
                          ->join('sales_orders','sales_orders.id','=','rehiring_orders.id_sales_order')
                          ->join('purchase_orders','purchase_orders.id','=','rehiring_orders.id_purchase_order')
                          ->where('next_step' ,'=', 'Sold')
                          ->select('rehiring_orders.*','sales_orders.agreement_no','purchase_orders.vehicle_registration')
                          ->get();
        if(count($rehiringorders) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $rehiringorders
            ],200);
        }
        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function show($id){
        $rehiringorder = RehiringOrder::find($id);

        if(!is_null($rehiringorder)){
            return response([
                'message' => 'Retrieve Rehiring Order Success',
                'data' => $rehiringorder
            ],200);
        }

        return response([
            'message' => 'Rehiring Order Not Found',
            'data' => null
        ],400);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'next_step'                  => 'required|in:Rehiring,Sold',
            'id_sales_order'             => 'nullable',
            'new_sales_order_no'         => 'nullable',
            'id_purchase_order'          => 'nullable',
            'vehicle_return_date'        => 'nullable',
            'sold_price'                 => 'nullable',
        ]);

        if($validate->fails())
            return response (['message' => $validate->errors()],400);

        $rehiringorder = RehiringOrder::create($storeData);

        $sales_order = SalesOrder::find($rehiringorder->id_sales_order);

        $purchaseorder = PurchaseOrder::find($rehiringorder->id_purchase_order);

        if($rehiringorder->next_step != 'Sold') {
            $purchaseorder->status_next_step = 'Available';
            $purchaseorder->save();
        } else {
            $purchaseorder->status_next_step = 'Sold';
            $purchaseorder->save();
        }

         if($rehiringorder->next_step != 'Sold') {
             $sales_order->next_step_status_sales = 'Innactive';
             $sales_order->save();
         } else {
             $sales_order->next_step_status_sales = 'Sold';
             $sales_order->save();
         }

        $amount_oi = RehiringOrder::join('other_incomes', 'other_incomes.id_purchase_order','=','rehiring_orders.id_purchase_order')
        ->whereRaw('rehiring_orders.id_purchase_order = '.$rehiringorder->id_purchase_order)
        ->value('amount_oi');

        $vehiclereturndate = \Carbon\Carbon::parse($request->vehicle_return_date);
        $contractstartdate = \Carbon\Carbon::parse($sales_order->contract_start_date);
        
        //fo001
        if($rehiringorder->vehicle_return_date != null) {
            $sales_order->margin_term = $contractstartdate->diffInMonths($vehiclereturndate);
            $sales_order->save();
        } 
           
        //fo002
        if($rehiringorder->next_step != 'Sold') {
            $sales_order->total_income = $sales_order->residual_value + $sales_order->initial_rental + ($sales_order->monthly_rental * ($sales_order->margin_term - 1) + $amount_oi);
            $sales_order->save();
        } else {
            $sales_order->total_income = $rehiringorder->sold_price + $sales_order->initial_rental + ($sales_order->monthly_rental * ($sales_order->margin_term - 1) + $amount_oi);
            $sales_order->save();
        }
         
        $rehiringorder->new_sales_order_no = IdGenerator::generate(['table' => 'rehiring_orders','field'=>'new_sales_order_no', 'length' => 8, 'prefix' =>'NSO-']);
        //output: P00001
        $rehiringorder->save();
        
        return response([
            'message' => 'Add Rehiring Order Success',
            'data' => $rehiringorder,
        ],200);
    }

    public function destroy($id){
        $rehiringorder = RehiringOrder::find($id);
       
        if(is_null($rehiringorder)){
            return response([
                'message' => 'Rehiring Order Not Found',
                'data' => null
            ],404);
        }

        if($rehiringorder->delete()){
            return response([
                'message' => 'Delete Rehiring Order Success',
                'data' => $rehiringorder,
            ],200);
        }
        
        return response([
            'message' => 'Delete Rehiring Order Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $rehiringorder = RehiringOrder::find($id);
        if(is_null($rehiringorder)){
            return response([
                'message' => 'Rehiring Order Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'next_step'                  => 'required|in:Rehiring,Sold',
            'id_sales_order'             => 'nullable',
            'new_sales_order_no'         => 'nullable',
            'id_purchase_order'          => 'nullable',
            'vehicle_return_date'        => 'nullable',
            'sold_price'                 => 'nullable',
        ]);

        if($validate->fails())
        return response(['message' => $validate->errors()],400);
        
        $rehiringorder->next_step                  = $updateData['next_step'];
        $rehiringorder->id_sales_order             = $updateData['id_sales_order'];
        $rehiringorder->new_sales_order_no         = $updateData['new_sales_order_no'];
        $rehiringorder->id_purchase_order          = $updateData['id_purchase_order'];
        $rehiringorder->vehicle_return_date        = $updateData['vehicle_return_date'];
        $rehiringorder->sold_price                 = $updateData['sold_price'];

        $sales_order = SalesOrder::find($rehiringorder->id_sales_order);

        $amount_oi = RehiringOrder::join('other_incomes', 'other_incomes.id_purchase_order','=','rehiring_orders.id_purchase_order')
        ->whereRaw('rehiring_orders.id_purchase_order = '.$rehiringorder->id_purchase_order)
        ->value('amount_oi');
        
        $vehiclereturndate = \Carbon\Carbon::parse($request->vehicle_return_date);
        $contractstartdate = \Carbon\Carbon::parse($sales_order->contract_start_date);
             
        //fo001
        if($rehiringorder->vehicle_return_date != null) {
            $sales_order->margin_term = $contractstartdate->diffInMonths($vehiclereturndate);
            $sales_order->save();
        }

        //fo002
        if($rehiringorder->next_step != 'Sold') {
            $sales_order->total_income = $sales_order->residual_value + $sales_order->initial_rental + ($sales_order->monthly_rental * ($sales_order->margin_term - 1) + $amount_oi);
            $sales_order->save();
        } else {
            $sales_order->total_income = $rehiringorder->sold_price + $sales_order->initial_rental + ($sales_order->monthly_rental * ($sales_order->margin_term - 1) + $amount_oi);
            $sales_order->save();
        }
        
        if($rehiringorder->save()){
            return response([
                'message' => 'Update Rehiring Order Success',
                'data' => $rehiringorder,
            ],200);
        }

        return response([
            'message' => 'Update Rehiring Order Failed',
            'data' => null
        ],400);
    }

    public function updateVehicleSold(Request $request, $id){
        $rehiringorder = RehiringOrder::find($id);
        if(is_null($rehiringorder)){
            return response([
                'message' => 'Rehiring Order Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'next_step'                  => 'required|in:Rehiring,Sold',
            'id_sales_order'             => 'nullable',
            'id_purchase_order'          => 'nullable',
            'vehicle_return_date'        => 'nullable',
            'sold_price'                 => 'nullable',
        ]);

        if($validate->fails())
        return response(['message' => $validate->errors()],400);
        
        $rehiringorder->next_step                  = $updateData['next_step'];
        $rehiringorder->id_sales_order             = $updateData['id_sales_order'];
        $rehiringorder->id_purchase_order          = $updateData['id_purchase_order'];
        $rehiringorder->vehicle_return_date        = $updateData['vehicle_return_date'];
        $rehiringorder->sold_price                 = $updateData['sold_price'];
        
        $sales_order = SalesOrder::find($rehiringorder->id_sales_order);

        $amount_oi = RehiringOrder::join('other_incomes', 'other_incomes.id_purchase_order','=','rehiring_orders.id_purchase_order')
        ->whereRaw('rehiring_orders.id_purchase_order = '.$rehiringorder->id_purchase_order)
        ->value('amount_oi');
        
        $vehiclereturndate = \Carbon\Carbon::parse($request->vehicle_return_date);
        $contractstartdate = \Carbon\Carbon::parse($sales_order->contract_start_date);
             
        //fo001
        if($rehiringorder->vehicle_return_date != null) {
            $sales_order->margin_term = $contractstartdate->diffInMonths($vehiclereturndate);
            $sales_order->save();
        }

        //fo002
        if($rehiringorder->next_step != 'Sold') {
            $sales_order->total_income = $sales_order->residual_value + $sales_order->initial_rental + ($sales_order->monthly_rental * ($sales_order->margin_term - 1) + $amount_oi);
            $sales_order->save();
        } else {
            $sales_order->total_income = $rehiringorder->sold_price + $sales_order->initial_rental + ($sales_order->monthly_rental * ($sales_order->margin_term - 1) + $amount_oi);
            $sales_order->save();
        }

        if($rehiringorder->save()){
            return response([
                'message' => 'Update Rehiring Order Success',
                'data' => $rehiringorder,
            ],200);
        }

        return response([
            'message' => 'Update Rehiring Order Failed',
            'data' => null
        ],400);
    }
    
}
