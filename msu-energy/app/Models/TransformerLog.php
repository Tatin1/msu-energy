<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransformerLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'recorded_at', 'frequency', 'v1', 'v2', 'v3',
        'a1', 'a2', 'a3', 'pf', 'kwh'
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];
}
