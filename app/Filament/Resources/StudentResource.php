<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        TextInput::make('id_no')
                            ->label('Student ID No')
                            ->required()
                            ->numeric()
                            ->type('number')
                            ->maxLength(10)
                            ->reactive()
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                if (!$state) return;

                                $exists = Student::where('id_no', $state)->exists();

                                if ($exists) {
                                    Notification::make()
                                        ->title('ID number already exists')
                                        ->danger()
                                        ->send();

                                    $set('id_no', null);
                                }
                            })
                            ->rule(function () {
                                return function (string $attribute, $value, $fail) {
                                    $exists = \App\Models\Student::where('id_no', $value)->exists();
                                    if ($exists) {
                                        $fail('This ID number already exists.');
                                    }
                                };
                            })
                            ->required(),
                        TextInput::make('first_name')
                            ->label('First Name')
                            ->required()
                            ->maxLength(150)
                            ->autocapitalize(),
                        TextInput::make('middle_name')
                            ->label('Middle Name')
                            ->maxLength(150)
                            ->autocapitalize(),
                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->required()
                            ->maxLength(150)
                            ->autocapitalize(),
                        TextInput::make('contact_no')
                            ->label('Contact No')
                            ->numeric()
                            ->maxLength(11)
                            ->minLength(11)
                            ->tel()
                            ->reactive()
                            ->placeholder('09991350266')
                            ->required()
                            ->helperText('Enter an 11-digit mobile number starting with 09')
                            ->rule(function (Get $get) {
                                return function (string $attribute, $value, $fail) {
                                    if (!preg_match('/^09\d{9}$/', $value)) {
                                        $fail('The contact number must start with 09 and be 11 digits.');
                                    }
                                };
                            }),
                        Textarea::make('address')
                            ->required()
                            ->label('Address')
                            ->extraAttributes(['style' => 'text-transform: uppercase;']),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_no')
                    ->label('Student ID')
                    ->searchable(),
                TextColumn::make('full_name')
                    ->label('Name')
                    ->getStateUsing(function ($record) {
                        $middleInitial = $record->middle_name ? strtoupper($record->middle_name[0]) . '.' : '';
                        return "{$record->last_name}, {$record->first_name} {$middleInitial}";
                    })
                    ->searchable(),
                TextColumn::make('contact_no')
                    ->label('Contact No'),
                TextColumn::make('address')
                    ->label('Address')
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
