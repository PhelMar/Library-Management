<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;


class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->autocapitalize(),
                TextInput::make('author')
                    ->label('Author')
                    ->required()
                    ->maxLength(255)
                    ->autocapitalize(),
                TextInput::make('year_published')
                    ->required()
                    ->maxLength(100)
                    ->autocapitalize(),
                Select::make('course_id')
                    ->relationship('course', 'course_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                FileUpload::make('img_book')
                    ->label('Book Images')
                    ->multiple()
                    ->maxFiles(3)
                    ->maxSize(5120)
                    ->image()
                    ->imageEditor()
                    ->directory('books')
                    ->preserveFilenames()
                    ->enableOpen()
                    ->enableDownload()
                    ->reorderable()
                    ->columnSpanFull()
                    ->required(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                    ->validationMessages([
                        'minFiles' => 'Please upload at least 1 image.',
                        'maxFiles' => 'You can upload a maximum of 3 images.',
                        'maxSize' => 'Each image must be 5MB or smaller.',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable(),
                TextColumn::make('author')
                    ->label('Author')
                    ->searchable(),
                TextColumn::make('year_published')
                    ->label('Year Published')
                    ->searchable(),
                ImageColumn::make('img_book')
                    ->label('Img Book')
                    ->disk('public')
                    ->circular(false)
                    ->getStateUsing(fn($record) => url('storage/' . ($record->img_book[0] ?? '')))
                    ->height(60)
                    ->width(60)



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
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }

    public static function mutateFormDataBeforeFill(array $data): array
    {
        $data['img_book'] = collect($data['img_book'] ?? [])
            ->filter(fn($path) => Storage::disk('public')->exists($path))
            ->map(fn($path) => Storage::url($path)) // full public URL like /storage/books/file.jpg
            ->values()
            ->toArray();

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['img_book'] = collect($data['img_book'] ?? [])
            ->map(function ($item) {
                // Strip URL back to relative path, e.g., books/Books_Python1.jpg
                return str_replace('storage/', '', ltrim(parse_url($item, PHP_URL_PATH), '/'));
            })
            ->toArray();

        return $data;
    }
}
