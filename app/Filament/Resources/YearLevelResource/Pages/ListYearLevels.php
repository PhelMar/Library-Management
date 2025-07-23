<?php

namespace App\Filament\Resources\YearLevelResource\Pages;

use App\Filament\Resources\YearLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListYearLevels extends ListRecords
{
    protected static string $resource = YearLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
