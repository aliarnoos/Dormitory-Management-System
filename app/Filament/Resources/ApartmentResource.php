<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApartmentResource\Pages;
use App\Filament\Resources\ApartmentResource\RelationManagers;
use App\Models\Apartment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Facades\Filament;

class ApartmentResource extends Resource
{
    protected static ?string $model = Apartment::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    

    // /**
    //  * @return Builder<Apartment>
    //  */
    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()->with(['rooms']);
    // }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Select::make('apartment_type')
                ->label('Apartment Type')
                ->options([
                    'standard' => 'Standard',
                    'private' => 'Private',
                    'economy' => 'Economy',
                ])
                ->required(),

            TextInput::make('floor')
                ->label('Floor')
                ->numeric()
                ->required(),

            Forms\Components\Select::make('gender')
                ->options(['male' => 'Male', 'female' => 'Female']),

            TextInput::make('number')
                ->label('Room Number')
                ->numeric()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->alignCenter()
                    ->label('#ID'),

                TextColumn::make('apartment_type')
                    ->alignCenter()
                    ->label('Apartment Type'),
                
                TextColumn::make('floor')
                    ->alignCenter()
                    ->label('Floor'),

                TextColumn::make('number')
                    ->alignCenter()
                    ->label('Room Number'),
                
                TextColumn::make('gender')
                    ->alignCenter()
                    ->label('Room for'),

            ])
            ->defaultGroup('apartment_type')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListApartments::route('/'),
            // 'create' => Pages\CreateApartment::route('/create'),
            // 'edit' => Pages\EditApartment::route('/{record}/edit'),
        ];
    }
}
