<?php

namespace App\Filament\Resources\YearLevelResource\Pages;

use App\Filament\Resources\YearLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditYearLevel extends EditRecord
{
    protected static string $resource = YearLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
