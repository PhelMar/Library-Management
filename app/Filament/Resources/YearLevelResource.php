<?php

namespace App\Filament\Resources;

use App\Filament\Resources\YearLevelResource\Pages;
use App\Filament\Resources\YearLevelResource\RelationManagers;
use App\Models\YearLevel;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class YearLevelResource extends Resource
{
    protected static ?string $model = YearLevel::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function getNavigationGroup(): ?string
    {
        return '📁 Data Source';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('year_level_name')
                    ->required()
                    ->autocapitalize()
                    ->maxLength(255)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('year_level_name')
                    ->label('Year Level')
                    ->searchable()
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
            'index' => Pages\ListYearLevels::route('/'),
            'create' => Pages\CreateYearLevel::route('/create'),
            'edit' => Pages\EditYearLevel::route('/{record}/edit'),
        ];
    }
}
