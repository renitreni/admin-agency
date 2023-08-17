<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
    ];

    public function fullname(): Attribute
    {
        return Attribute::make(get: fn ($v, $attr) => $attr['first_name'] . ' ' . $attr['last_name']);
    }
}
