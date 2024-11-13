<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;

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

        Forms\Components\Textarea::make('issue')
            ->required(),   
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();
        
        // $data['user_id'] = Filament::auth()->user()->id;
        // $data['status'] = 'pending';   
        
        
        // Reservation::create($data);

        Notification::make()
            ->title('Reservation created successfully!')
            ->success()
            ->send();

        $this->form->fill(); 
    }

}
