<?php

namespace App\Filament\Resources\BorrowResource\Pages;

use App\Filament\Resources\BorrowResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Carbon;

class EditBorrow extends EditRecord
{
    protected static string $resource = BorrowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('returnBook')
                ->label('Mark as Returned')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->visible(fn($record) => $record->status !== 'Returned')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status' => 'Returned',
                        'returned_date' => Carbon::now()->toDateString(),
                    ]);
                    $this->notify('success', 'Book marked as returned.');
                }),
        ];
    }
}
