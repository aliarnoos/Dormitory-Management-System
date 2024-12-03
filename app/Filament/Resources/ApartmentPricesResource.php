<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApartmentPricesResource\Pages;
use App\Filament\Resources\ApartmentPricesResource\RelationManagers;
use App\Models\ApartmentPrices;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Facades\Filament;

class ApartmentPricesResource extends Resource
{
    protected static ?string $model = ApartmentPrices::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('apartment_type')
                ->label('Apartment Type')
                ->required()
                ->options([
                    'economy' => 'Economy',
                    'standard' => 'Standard',
                    'private' => 'Private',
                ]),
                Forms\Components\TextInput::make('ug_semester_price')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('app_semester_price')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('winter_price')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('summer_price')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('apartment_type')
                    ->label('Apartment Type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ug_semester_price')
                    ->label('UG - Full Semester')
                    ->searchable(),
                Tables\Columns\TextColumn::make('app_semester_price')
                    ->label(label: 'APP - Full Semester')
                    ->searchable(),
                Tables\Columns\TextColumn::make('winter_price')
                    ->label('Winter Semester')
                    ->searchable(),
                Tables\Columns\TextColumn::make('summer_price')
                    ->label('Summer Semester')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canViewAny(): bool
    {
        return Filament::auth()->user()->role === 'fmd';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApartmentPrices::route('/'),
        ];
    }
}
