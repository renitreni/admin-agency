<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'contact_number',
        'date_hired',
        'address',
        'date_birth',
        'place_birth',
        'passport_number',
        'passport_place_issue',
        'passport_date_issue',
        'passport_date_expired',
        'elementary',
        'high_school',
        'vocational',
        'college',
        'father_name',
        'father_occupation',
        'mother_name',
        'mother_occupation',
        'spouse_name',
        'spouse_occupation',
        'gender',
        'religion',
        'civil_status',
        'height',
        'weight',
        'objectives',
        'pic_face',
        'pic_body'
    ];
}
