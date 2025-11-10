<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Meter extends Model {
    protected $fillable = ['building_id','meter_code','label'];
    public function building(){ return $this->belongsTo(Building::class); }
    public function readings(){ return $this->hasMany(Reading::class); }
}
