<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SalesOrder extends Model
{
    protected $fillable = [
        'id_purchase_order','type','agreement_no','cust_name','sales_person','contract_start_date','vehicle_manufacturer','vehicle_model',
        'vehicle_variant','basic_list_price','residual_value','annual_mileage',
        'term_months','initial_rental','documentation_fees','monthly_rental','other_income','margin_term','total_income','next_step_status_sales'
    ];

    public function getCreatedAtAttribute(){
        if(!is_null($this->attributes['created_at'])){
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
        }
    }

    public function getUpdatedAtAttribute(){
        if(!is_null($this->attributes['updated_at'])){
            return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
        }
    }
}
