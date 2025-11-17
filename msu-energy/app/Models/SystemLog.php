<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;

    protected $table = 'system_logs';

    protected $fillable = [
        'building', // added building
        'date',
        'time',
        'time_ed',
        'total_kw',
        'total_kvar',
        'total_kva',
        'total_pf',
    ];
}
