<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransformerLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'meter_id',
        'date',
        'time',
        'time_ed',
        'recorded_at',
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
        'kwiii',
        'kvaiii',
        'kvariii',
        'pfiii',
        'kwh',
        'cost'
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];
}
