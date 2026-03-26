<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reading extends Model
{
    use HasFactory;

    protected $fillable = [
        'meter_id',
        'voltage1',
        'voltage2',
        'voltage3',
        'current1',
        'current2',
        'current3',
        'kw1',
        'kw2',
        'kw3',
        'pf1',
        'pf2',
        'pf3',
        'power_factor',
        'active_power',
        'reactive_power',
        'apparent_power',
        'kwh',
        'cost',
        'recorded_at',
    ];

    protected $dates = ['recorded_at'];

    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }
}
