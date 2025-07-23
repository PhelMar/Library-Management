<?php

namespace App\Filament\Resources\YearLevelResource\Pages;

use App\Filament\Resources\YearLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateYearLevel extends CreateRecord
{
    protected static string $resource = YearLevelResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
