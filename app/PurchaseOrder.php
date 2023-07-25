<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'id_rehiring_order','id_sales_order','purchase_method','vehicle_registration','hp_finance_provider','hire_purchase_starting_date','hp_interest_per_annum',
        'hp_deposit_amount','hp_term','documentation_fees_pu','final_fees','other_fees','price_otr',
        'monthly_payment','final_payment','hp_interest_type','financing_amount','regular_monthly_payment','status_next_step'
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
