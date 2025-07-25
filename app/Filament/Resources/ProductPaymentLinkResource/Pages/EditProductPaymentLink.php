<?php

namespace App\Filament\Resources\ProductPaymentLinkResource\Pages;

use App\Filament\Resources\ProductPaymentLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductPaymentLink extends EditRecord
{
    protected static string $resource = ProductPaymentLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
