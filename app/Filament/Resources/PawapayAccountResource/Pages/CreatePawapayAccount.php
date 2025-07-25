<?php

namespace App\Filament\Resources\PawapayAccountResource\Pages;

use App\Filament\Resources\PawapayAccountResource;
use App\Models\PawapayAccount;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePawapayAccount extends CreateRecord
{
    protected static string $resource = PawapayAccountResource::class;

    protected function beforeCreate(): void
    {
        if ($this->data['is_default']) {
            PawapayAccount::where('is_default', true)->update(['is_default' => false]);
        }
    }

    protected function beforeSave(): void
    {
        if ($this->data['is_default']) {
            PawaPayAccount::where('is_default', true)->where('id', '!=', $this->record->id)->update(['is_default' => false]);
        }
    }
}
