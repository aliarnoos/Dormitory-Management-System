<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InspectionResource\Pages;
use App\Filament\Resources\InspectionResource\RelationManagers;
use App\Models\Inspection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Facades\Filament;

class InspectionResource extends Resource
{
    protected static ?string $model = Inspection::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    /**
     * @return Builder<Inspection>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['reservation']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('reservation_id')
                ->label('Reservation')
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

                Forms\Components\DateTimePicker::make('inspection_date')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                    'Pending' => 'pending',
                    'Done' => 'done',
                    'Fined' => 'fined',
                    ])                    
                    ->required()
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
                Tables\Columns\TextColumn::make('inspection_date')
                    ->dateTime()
                    ->sortable(),
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
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListInspections::route('/'),
            // 'create' => Pages\CreateInspection::route('/create'),
            // 'edit' => Pages\EditInspection::route('/{record}/edit'),
        ];
    }
}
