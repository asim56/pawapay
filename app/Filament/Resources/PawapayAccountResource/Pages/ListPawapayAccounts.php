<?php

namespace App\Filament\Resources\PawapayAccountResource\Pages;

use App\Filament\Resources\PawapayAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPawapayAccounts extends ListRecords
{
    protected static string $resource = PawapayAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
