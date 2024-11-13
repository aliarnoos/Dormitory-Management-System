<?php

namespace App\Filament\Pages;

use App\Models\Inspection;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;
use App\Models\Reservation;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions;

class RequestInspection extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static string $view = 'filament.pages.request-inspection';

    public ?array $data = [];


    protected static ?string $navigationLabel = 'Request Inspection';

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
        return [
            Forms\Components\DateTimePicker::make('inspection_date')
            ->required(),
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();
    
        // Get the current user's ID
        $userId = Filament::auth()->user()->id;
    
        // Find the latest reservation for the user
        $latestReservation = Reservation::where('user_id', $userId)
            ->latest('created_at') // Or 'id' if you prefer
            ->first();
    
        // If a reservation exists, use its ID; otherwise, handle appropriately
        if ($latestReservation) {
            $data['reservation_id'] = $latestReservation->id;
        } else {
            // Handle the case where there is no reservation for the user
            Notification::make()
                ->title('No reservation found for this user.')
                ->danger()
                ->send();
    
            return;
        }
    
        // Set the status to 'pending'
        $data['status'] = 'pending';
    
        // Create the inspection
        Inspection::create($data);
    
        // Notify the user of success
        Notification::make()
            ->title('Inspection requested successfully!')
            ->success()
            ->send();
    
        // Reset the form
        $this->form->fill();
    }
    

}
