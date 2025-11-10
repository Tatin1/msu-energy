<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Billing extends Model {
    protected $fillable=['building_id','last_month_kwh','this_month_kwh','total_bill'];
    public function building(){ return $this->belongsTo(Building::class); }
}
