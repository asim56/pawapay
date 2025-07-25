<?php

namespace App\Filament\Resources\ProductPaymentLinkResource\Pages;

use App\Filament\Resources\ProductPaymentLinkResource;
use App\Models\PawapayAccount;
use App\Models\ProductPaymentLink;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateProductPaymentLink extends CreateRecord
{
    protected static string $resource = ProductPaymentLinkResource::class;

    public function getTitle(): string
    {
        return 'Generate Link';
    }

    protected function beforeCreate(): void
    {
        if (! PawaPayAccount::where('is_default', true)->exists()) {
            Notification::make()
                ->title('No default PawaPay account')
                ->body('Please create or set a default account before proceeding.')
                ->danger()
                ->send();

            $this->redirect(ProductPaymentLinkResource::getUrl());
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $defaultAccount = PawaPayAccount::where('is_default', true)->first();

        if (! $defaultAccount) {
            throw ValidationException::withMessages([
                'name' => 'No default PawaPay account is set. Please create or set one before proceeding.',
            ]);
        }

        $data['reference_id'] = (string) Str::uuid();
        $pawapayAccount = PawapayAccount::where("is_default", 1)->first();
        $data['price'] = round(($data["product_price"] + $data["product_fee"]), 2);
        $data['pawapay_account_id'] = $pawapayAccount->id;
        $data['status'] = 'pending';

        return $data;
    }

    protected function handleRecordCreation(array $data): ProductPaymentLink
    {
        $record = static::getModel()::create($data);

        // Flash URL in session to use in view
        session()->flash('payment_url', url("product/payment/{$record->reference_id}"));

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('view', ['record' => $this->record]);
    }
}
