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
use Filament\Facades\Filament;
use App\Models\Room;
use App\Notifications\ReservationApproved;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Filters\SelectFilter;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';
    

    /**
     * @return Builder<Reservation>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['room', 'user']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->options( \App\Models\User::where('role', 'student')->get()->pluck('name', 'id'))
                    ->searchable()
                    ->label('Student Name')
                    ->disabledOn('edit')
                    ->getOptionLabelUsing(fn ($value) => \App\Models\User::find($value)?->name)
                    ->required(),

                Forms\Components\Select::make('room_id')
                    ->label('Room')
                    ->options(function () {
                        return Room::with('apartment')
                            ->whereHas('apartment')
                            ->get()
                            ->mapWithKeys(function ($room) {
                                return [
                                    $room->id => "Apartment {$room->apartment->number} - Floor {$room->apartment->floor} - Room {$room->room_number}",
                                ];
                            });
                    })
                    ->required()
                    ->searchable()
                    ->getOptionLabelUsing(fn ($value) => \App\Models\Room::find($value)?->number),
                
                Forms\Components\Select::make('status')
                    ->required()
                    ->options(['Pending' => 'pending', 'Canceled' => 'canceled', 'Completed' => 'completed'])
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
                    ->label('Student Name')
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
                
                Tables\Columns\IconColumn::make('user.has_deposit')
                    ->label('Has Deposit')
                    ->boolean()
                    ->alignCenter()
                    ->sortable(),

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
                SelectFilter::make('semester')
                    ->label('Semester')
                    ->options([
                        'winter' => 'Winter',
                        'summer' => 'Summer',
                        'fall' => 'Fall',
                        'spring' => 'Spring',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->action(function ($record, $data) {
                        Reservation::where('id', $record->id)->update([
                            'status' => 'completed'
                        ]);

                        $record->user->notify(new ReservationApproved($data['date']));
                    })
                    ->form([
                        DateTimePicker::make('date')->required()
                    ])
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

    public static function canViewAny(): bool
    {
        return Filament::auth()->user()->role === 'fmd';
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
