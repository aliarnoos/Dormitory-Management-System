<?php

namespace App\Filament\Resources\ApartmentPricesResource\Pages;

use App\Filament\Resources\ApartmentPricesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApartmentPrices extends ListRecords
{
    protected static string $resource = ApartmentPricesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
