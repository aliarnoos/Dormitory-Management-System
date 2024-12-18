<?php

namespace App\Filament\Pages;

use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Apartment;
use Filament\Forms\Concerns\InteractsWithForms;

class CreateReservation extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    public ?string $selectedApartmentType = null; // Track the selected apartment type

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
        $userGender = Filament::auth()->user()->gender;

 
        return [
            // Program Type Select
            Forms\Components\Select::make('program')
                ->label('Program')
                ->required()
                ->options([
                    'UG' => 'Undergraduate (UG)',
                    'APP' => 'Academic Preparatory Program (APP)',
                ])
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $set('semester', null); // Reset semester when program changes
                    $set('room_price', null); // Reset price when program changes
                }),
    
            // Semester Select
            Forms\Components\Select::make('semester')
                ->label('Semester')
                ->required()
                ->options([
                    'fall' => 'Fall',
                    'winter' => 'Winter',
                    'spring' => 'Spring',
                    'summer' => 'Summer',
                ])
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $program = $get('program');
                    $roomType = $get('apartment_type');
                    if ($program && $state && $roomType) {
                        $price = $this->getPrice($program, $state, $roomType);
                        $set('room_price', $price);
                    }
                }),
    
            // Apartment Type Select
            Forms\Components\Select::make('apartment_type')
                ->label('Apartment Type')
                ->required()
                ->options([
                    'economy' => 'Economy',
                    'standard' => 'Standard',
                    'private' => 'Private',
                ])
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $program = $get('program');
                    $semester = $get('semester');
                    if ($program && $semester && $state) {
                        $price = $this->getPrice($program, $semester, $state);
                        $set('room_price', $price);
                    }
                }),
    
            // Room Select
            Forms\Components\Select::make('room_id')
                ->label('Room')
                ->disabled(fn($get) => $get('apartment_type') == null)
                ->options(function ($get) use ($userGender) {
                    return Room::with('apartment')
                        ->whereHas('apartment', function ($query) use ($userGender, $get) {
                            $query->where('gender', $userGender)->where('apartment_type', $get('apartment_type'));
                        })
                        ->get()
                        ->mapWithKeys(function ($room) {
                            return [
                                $room->id => "Apartment {$room->apartment->number} - Floor {$room->apartment->floor} - Room {$room->room_number}",
                            ];
                        });
                })
                ->required()
                ->searchable(),
    
            // Display Room Price
            Forms\Components\TextInput::make('room_price')
                ->label('Price')
                ->disabled()
                ->columnSpan('full'),
    
            // Year Input
            Forms\Components\TextInput::make('year')
                ->required()
                ->numeric()
                ->minValue(2000)
                ->maxValue(2030)
                ->default('2024'),
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

    private function getPrice(string $program, string $semester, string $apartmentType): string
    {
        // Fetch the price data using Eloquent
        $priceData = \App\Models\ApartmentPrices::where('apartment_type', $apartmentType)->first()->toArray();
    
        if (!$priceData) {
            return 0; // Return 0 if no matching data is found
        }
    
        // Determine which column to use based on the program and semester
        $column = match ($program) {
            'UG' => match ($semester) {
                'fall', 'spring' => 'ug_semester_price',
                'winter' => 'winter_price',
                'summer' => 'summer_price',
                default => null,
            },
            'APP' => match ($semester) {
                'fall', 'spring' => 'app_semester_price',
                'winter' => 'winter_price',
                'summer' => 'summer_price',
                default => null,
            },
            default => null,
        };
    
        // Return the price from the appropriate column
        return $column ?  $priceData[$column] . 'IQD' : 0;
    }
    
    

}
