<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgencyUser extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id',
        'agency_id',
    ];
}
