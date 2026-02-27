<?php

namespace App\Filament\Resources\AgencyForCurrentUserResource\Pages;

use App\Filament\Resources\AgencyForCurrentUserResource;
use Filament\Resources\Pages\ListRecords;

class ListAgenciesForCurrentUser extends ListRecords
{
    protected static string $resource = AgencyForCurrentUserResource::class;
}
