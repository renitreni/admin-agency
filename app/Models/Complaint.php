<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Complaint extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'foreign_recruitment_agency',
        'ofw_full_name',
        'gender',
        'birthdate',
        'occupation',
        'nation_id',
        'passport_no',
        'email',
        'contact_person',
        'primary_contact',
        'secondary_contact',
        'address_abroad',
        'complaint',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image_evidences')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }
}
