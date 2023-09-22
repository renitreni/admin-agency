<?php

namespace App\Models;

use Carbon\Carbon;
use \Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'company_name',
        'address',
        'from_date',
        'to_date',
        'position',
    ];
}