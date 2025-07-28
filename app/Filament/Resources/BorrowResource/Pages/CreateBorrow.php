<?php

namespace App\Filament\Resources\BorrowResource\Pages;

use App\Filament\Resources\BorrowResource;
use App\Models\Book;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Validation\ValidationException;

class CreateBorrow extends CreateRecord
{
    protected static string $resource = BorrowResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'Borrowed';

        $book = Book::find($data['book_id']);
        if (!$book || $book->quantity <= 0) {
            Notification::make()
                ->title('This book is currently out of stock.')
                ->danger()
                ->send();

            throw new Halt();
        }

        $book->decrement('quantity');
        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
