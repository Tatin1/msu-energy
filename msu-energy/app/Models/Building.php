<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Building extends Model {
    protected $fillable = ['code','name','is_online'];
    public function meters(){ return $this->hasMany(Meter::class); }
    public function billing(){ return $this->hasOne(Billing::class); }
}
