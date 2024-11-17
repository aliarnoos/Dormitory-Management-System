<?php

namespace App\Filament\Pages;

use App\Models\Maintenance;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Facades\Filament;
use App\Models\Reservation;

class RequestMaintenance extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string $view = 'filament.pages.request-maintenance';

    public ?array $data = [];

    protected static ?string $navigationLabel = 'Request Maintenance';

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
        Forms\Components\TextInput::make('type')
            ->required()
            ->maxLength(255),
        Forms\Components\Textarea::make('description')
            ->required()
            ->columnSpanFull(),
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();

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
        
        $data['status'] = 'pending';   
        
        Maintenance::create($data);

        Notification::make()
            ->title('Request sent successfully!')
            ->success()
            ->send();

        $this->form->fill(); 
    }

}
