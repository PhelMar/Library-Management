<?php

namespace App\Filament\Resources\StudentRecordResource\Pages;

use App\Filament\Resources\StudentRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentRecord extends CreateRecord
{
    protected static string $resource = StudentRecordResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
