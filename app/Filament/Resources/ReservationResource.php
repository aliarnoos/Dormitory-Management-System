<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Filament\Resources\ReservationResource\RelationManagers;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    /**
     * @return Builder<Reservation>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['room']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->disabledOn('edit')
                    ->getOptionLabelUsing(fn ($value) => \App\Models\User::find($value)?->name)
                    ->required(),

                Forms\Components\Select::make('room_id')
                    ->label('Room')
                    ->options(function () {
                        return \App\Models\Apartment::with('rooms')->get()->flatMap(function ($apartment) {
                            return $apartment->rooms->mapWithKeys(function ($room) use ($apartment) {
                                return [
                                    $room->id => "Apartment {$apartment->number} - Floor {$apartment->floor} - Room {$room->room_number}"
                                ];
                            });
                        });
                    })
                    ->required()
                    ->searchable()
                    ->getOptionLabelUsing(fn ($value) => \App\Models\Room::find($value)?->number),
                
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('pending'),
                Forms\Components\Select::make('semester')
                    ->required()
                    ->options([
                        'winter' => 'Winter',
                        'summer' => 'Summer',
                        'fall' => 'Fall',
                        'spring' => 'Spring',
                    ]),

                Forms\Components\TextInput::make('year')
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(2030)
                    ->default('2024')          
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('user.email')
                    ->alignCenter()
                    ->label('Email'),

                Tables\Columns\TextColumn::make('room_id')
                    ->label('Room')
                    ->alignCenter()
                    ->formatStateUsing(function ($record) {
                        return "Apartment {$record->room->apartment->number} - Room {$record->room->room_number}";
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('semester')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListReservations::route('/'),
            // 'create' => Pages\CreateReservation::route('/create'),
            // 'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}
