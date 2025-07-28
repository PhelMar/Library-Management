<?php

namespace App\Filament\Resources\BorrowResource\Pages;

use App\Filament\Resources\BorrowResource;
use App\Models\Book;
use Filament\Actions;
use Filament\Notifications\Notification;
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
                ->action(function ($record) {
                    $record->update([
                        'status' => 'Returned',
                        'returned_date' => now()->toDateString(),
                    ]);

                    $book = Book::find($record->book_id);
                    if ($book) {
                        $book->increment('quantity');
                    }


                    Notification::make()
                        ->title('Book marked as returned.')
                        ->success()
                        ->send();
                    return redirect(static::getResource()::getUrl('index'));
                }),



        ];
    }
}
