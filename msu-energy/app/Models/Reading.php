<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reading extends Model
{
    use HasFactory;

    protected $fillable = [
        'meter_id',
        'frequency',
        'v1',
        'v2',
        'v3',
        'a1',
        'a2',
        'a3',
        'kw1',
        'kw2',
        'kw3',
        'pf1',
        'pf2',
        'pf3',
        'pfiii',
        'kwiii',
        'kvariii',
        'kvaiii',
        'kwhiii',
        'cost',
        'recorded_at',
    ];

    protected $dates = ['recorded_at'];

    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }
}
