<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentRecordResource\Pages;
use App\Filament\Resources\StudentRecordResource\RelationManagers;
use App\Models\StudentRecord;
use Filament\Actions\SelectAction;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentRecordResource extends Resource
{
    protected static ?string $model = StudentRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('student_id')
                    ->relationship('student', 'id_no')
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('course_id')
                    ->relationship('course', 'course_name')
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('school_year_id')
                    ->label('School Year')
                    ->options(function () {
                        return \App\Models\SchoolYear::latest()
                            ->take(5)
                            ->pluck('school_year_name', 'id');
                    })
                    ->default(function () {
                        return \App\Models\SchoolYear::latest()->value('id');
                    })
                    ->disabled(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord) // visually disabled
                    ->dehydrated(fn($livewire) => true) // still submit value
                    ->required(),


                Select::make('semester_id')
                    ->relationship('semester', 'semester_name')
                    ->preload()
                    ->required(),
                Select::make('year_level_id')
                    ->relationship('year_level', 'year_level_name')
                    ->preload()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.id_no')
                    ->label('Student Id')
                    ->searchable(),
                TextColumn::make('student.full_name')
                    ->label('Student Name')
                    ->searchable(),
                TextColumn::make('course.course_name')
                    ->searchable(),
                TextColumn::make('school_year.school_year_name')
                    ->searchable(),
                TextColumn::make('semester.semester_name')
                    ->searchable(),
                TextColumn::make('year_level.year_level_name')
                    ->searchable(),

            ])
            ->filters([
                SelectFilter::make('course_id')
                    ->label('Course')
                    ->relationship('course', 'course_name'),
                SelectFilter::make('school_year_id')
                    ->label('School Year')
                    ->relationship('school_year', 'school_year_name')
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
            'index' => Pages\ListStudentRecords::route('/'),
            'create' => Pages\CreateStudentRecord::route('/create'),
            'edit' => Pages\EditStudentRecord::route('/{record}/edit'),
        ];
    }
}
