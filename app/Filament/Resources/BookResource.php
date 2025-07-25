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
use Filament\Tables\Filters\SelectFilter;
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
                SelectFilter::make('course_id')
                    ->label('Course')
                    ->relationship('course', 'course_name')
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
}
