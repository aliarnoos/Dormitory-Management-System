<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceResource\Pages;
use App\Filament\Resources\MaintenanceResource\RelationManagers;
use App\Models\Maintenance;
use App\Notifications\MaintenanceNotification;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Facades\Filament;

class MaintenanceResource extends Resource
{
    protected static ?string $model = Maintenance::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('reservation_id')
                    ->options(function () {
                        return \App\Models\Reservation::with('user')
                            ->get()
                            ->mapWithKeys(function ($reservation) {
                                return [
                                    $reservation->id => "{$reservation->user->name} - {$reservation->semester} {$reservation->year}"
                                ];
                            });
                    })                    
                    ->required(),
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('pending'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reservation.id')
                    ->numeric()
                    ->getStateUsing(function ($record) {
                        return $record->reservation->user->name ?? 'N/A'; 
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('room_details')
                    ->label('Room Details')
                    ->getStateUsing(function ($record) {
                        $reservation = $record->reservation;
    
                        if ($reservation && $reservation->room) {
                            $floor = $reservation->room->apartment->floor ?? 'N/A';
                            $roomNumber = $reservation->room->room_number ?? 'N/A';
                            return "Floor {$floor}, Room {$roomNumber}";
                        }
    
                        return 'N/A';
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
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
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('send-email')
                    ->label('Send Email')
                    ->form([
                        DateTimePicker::make('date')->required()
                    ])
                    ->action(function ($record, $data) {
                        $record->reservation->user->notify(new MaintenanceNotification($data['date']));
                    })
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
            'index' => Pages\ListMaintenances::route('/'),
            // 'create' => Pages\CreateMaintenance::route('/create'),
            // 'edit' => Pages\EditMaintenance::route('/{record}/edit'),
        ];
    }
}
