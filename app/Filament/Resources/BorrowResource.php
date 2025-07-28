<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BorrowResource\Pages;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\StudentRecord;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Carbon;

class BorrowResource extends Resource
{
    protected static ?string $model = Borrow::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('student_record_id')
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
                            ? $record->student->id_no . ' - ' . $record->student->full_name
                            : null;
                    })
                    ->reactive()
                    ->afterStateUpdated(function (Get $get, $state) {
                        $activeBorrowCount = \App\Models\Borrow::where('student_record_id', $state)
                            ->where('status', 'Borrowed')
                            ->count();

                        if ($activeBorrowCount >= 3) {
                            Notification::make()
                                ->title('Borrowing Limit Reached')
                                ->body('This student has already borrowed 3 books and must return one before borrowing more.')
                                ->danger()
                                ->send();
                        }
                    })
                    ->rule(function (Get $get) {
                        return function (string $attribute, $value, $fail) {
                            $borrowCount = \App\Models\Borrow::where('student_record_id', $value)
                                ->where('status', 'Borrowed')
                                ->count();

                            if ($borrowCount >= 3) {
                                $fail('This student has already borrowed the maximum of 3 books.');
                            }
                        };
                    })
                    ->hint(function (Get $get) {
                        $id = $get('student_record_id');
                        if (!$id) return null;

                        $count = \App\Models\Borrow::where('student_record_id', $id)
                            ->where('status', 'Borrowed')
                            ->count();

                        return "Currently borrowed: {$count}/3";
                    })
                    ->required(),
                Select::make('book_id')
                    ->label('Book Title')
                    ->searchable()
                    ->reactive()
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
                                    $book->id => $book->title . ' - (' . $book->quantity . ' available)',
                                ];
                            })
                            ->toArray();
                    })
                    ->getOptionLabelUsing(function ($value): ?string {
                        $book = Book::find($value);
                        return $book ? $book->title . ' - ' . $book->author : null;
                    })
                    ->afterStateUpdated(function (Get $get, $state) {
                        $book = \App\Models\Book::find($state);

                        if ($book && $book->quantity <= 0) {
                            Notification::make()
                                ->title('Out of Stock')
                                ->body('This book is currently not available for borrowing.')
                                ->danger()
                                ->send();
                        }
                    })
                    ->required(),
                DatePicker::make('due_date')
                    ->label('Due Date')
                    ->minDate(now())
                    ->disabledDates(function () {
                        $disabled = [];

                        for ($i = 0; $i <= 7; $i++) {
                            $date = Carbon::now()->addDays($i);
                            if ($date->isSunday()) {
                                $disabled[] = $date->toDateString();
                            }
                        }
                        return $disabled;
                    })
                    ->maxDate(Carbon::now()->addDays(7))
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                return $query->with('student_record.student');
            })
            ->columns([
                TextColumn::make('student_record.student.full_name')
                    ->label('Student Name')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($state) => $state ?? 'N/A'),
                TextColumn::make('book.title')
                    ->label('Book Title')
                    ->searchable(),
                TextColumn::make('book.author')
                    ->label('Author')
                    ->searchable(),
                TextColumn::make('borrowed_date')
                    ->label('Borrowed Date'),
                TextColumn::make('due_date')
                    ->label('Due Date'),
                TextColumn::make('returned_date')
                    ->label('Returned Date'),
                TextColumn::make('status')
                    ->label('Status'),
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
