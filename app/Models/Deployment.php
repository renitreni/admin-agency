<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deployment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'position',
        'country',
        'date_deployed',
        'status',
        'agency_id',
        'worker_id',
        'foreign_agency_id',
        'handler_id'
    ];
}
