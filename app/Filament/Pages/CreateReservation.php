<?php

namespace App\Filament\Pages;

use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;
use App\Models\Reservation;
use App\Models\Room;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions;

class CreateReservation extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.create-reservation';

    protected static ?string $navigationLabel = 'Create Reservation';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user() && auth()->user()->role === 'student'; 
    }

    public function mount(): void
    {
        $this->form->fill(); 
    }

public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema($this->getFormSchema())
            ->statePath('data');
    }

    protected function getFormSchema(): array
    {
        $userGender = Filament::auth()->user()->gender; // Assuming 'gender' is stored in the users table.

        return [

            Forms\Components\Select::make('room_id')
            ->label('Room')
            ->options(function () use ($userGender) {
                return Room::with('apartment')
                    ->whereHas('apartment', function ($query) use ($userGender) {
                        $query->where('gender', $userGender); // Match room's apartment gender to user's gender
                    })
                    ->get()
                    ->mapWithKeys(function ($room) {
                        return [
                            $room->id => "Apartment {$room->apartment->number} - Floor {$room->apartment->floor} - Room {$room->room_number}",
                        ];
                    });
            })
            ->required()
            ->searchable()
            ->getOptionLabelUsing(fn ($value) => \App\Models\Room::find($value)?->room_number),
        
        

        Forms\Components\Select::make('semester')
            ->required()
            ->options([
                'winter' => 'Winter',
                'summer' => 'Summer',
                'fall' => 'Fall',
                'spring' => 'Spring',
            ]),

        Forms\Components\TextInput::make('year')
            ->required()
            ->numeric()
            ->minValue(2000)
            ->maxValue(2030)
            ->default('2024')     
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();
        
        $data['user_id'] = Filament::auth()->user()->id;
        $data['status'] = 'pending';   
        
        
        Reservation::create($data);

        Notification::make()
            ->title('Reservation created successfully!')
            ->success()
            ->send();

        $this->form->fill(); 
    }

    
}
