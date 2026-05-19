<?php

namespace App\Filament\Resources\DeployedWorkerForResource\Pages;

use App\Filament\Resources\DeployedWorkerForResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListDeployedWorkers extends ListRecords
{
    protected static string $resource = DeployedWorkerForResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->visible(false),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Workers'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return static::$resource::getEloquentQuery();
    }
}