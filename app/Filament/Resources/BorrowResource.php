<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BorrowResource\Pages;
use App\Filament\Resources\BorrowResource\RelationManagers;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\StudentRecord;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BorrowResource extends Resource
{
    protected static ?string $model = Borrow::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('student_records_id')
                    ->label('Student ID No')
                    ->searchable()
                    ->getSearchResultsUsing(function (?string $search) {
                        return StudentRecord::with('student')
                            ->when($search, function ($query, $search) {
                                $query->whereHas('student', function ($q) use ($search) {
                                    $q->where('id_no', 'like', "%{$search}%")
                                        ->orWhere('last_name', 'like', "%{$search}%");
                                });
                            }, function ($query) {
                                $query->limit(5);
                            })
                            ->get()
                            ->mapWithKeys(function ($record) {
                                return [
                                    $record->id => $record->student->id_no . ' - ' . $record->student->last_name . ', ' . $record->student->first_name,
                                ];
                            })
                            ->toArray();
                    })
                    ->getOptionLabelUsing(function ($value): ?string {
                        $record = StudentRecord::with('student')->find($value);
                        return $record
                            ? $record->student->id_no . ' - ' . $record->student->name
                            : null;
                    })
                    ->required(),
                Select::make('book_id')
                    ->label('Book Title')
                    ->searchable()
                    ->getSearchResultsUsing(function (?string $search) {
                        return Book::query()
                            ->when($search, function ($query, $search) {
                                $query->where('title', 'like', "%{$search}%")
                                    ->orWhere('author', 'like', "%{$search}%");
                            }, function ($query) {
                                $query->limit(5);
                            })
                            ->get()
                            ->mapWithKeys(function ($book) {
                                return [
                                    $book->id = $book->title . ' - ' . $book->author,
                                ];
                            })
                            ->toArray();
                    })
                    ->getOptionLabelUsing(function ($value): ?string {
                        $book = Book::find($value);
                        return $book ? $book->title . ' - ' . $book->author : null;
                    })
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBorrows::route('/'),
            'create' => Pages\CreateBorrow::route('/create'),
            'edit' => Pages\EditBorrow::route('/{record}/edit'),
        ];
    }
}
