<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingLog extends Model
{
    use HasFactory;

    protected $table = 'building_logs';

    protected $fillable = [
        'date', 'time', 'time_ed', 'f',
        'v1', 'v2', 'v3',
        'a1', 'a2', 'a3',
        'pf1', 'pf2', 'pf3',
        'kwh',
    ];
}
