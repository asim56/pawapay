<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductPaymentLinkResource\Pages;
use App\Filament\Resources\ProductPaymentLinkResource\RelationManagers;
use App\Models\PawapayAccount;
use App\Models\ProductPaymentLink;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class ProductPaymentLinkResource extends Resource
{
    protected static ?string $model = ProductPaymentLink::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $defaultAccount = PawapayAccount::where('is_default', true)->first();
        return $form
            ->schema([
                Forms\Components\Card::make([
                    Forms\Components\Placeholder::make('default_account')
                        ->label('PawaPay Account Name: ')
                        ->content($defaultAccount?->name ?? 'No default account found')
                        ->extraAttributes([
                            'class' => 'text-xl  font-semibold text-primary-600 text-primary-60 p-4 rounded-md shadow-sm',
                        ]),
                ])
                    ->columnSpan('full'),

                TextInput::make('name')
                    ->label('Product Name')
                    ->required()
                    ->minLength(4),

                TextInput::make('product_price')
                    ->numeric()
                    ->step(0.0001)
                    ->required(),
                TextInput::make('product_fee')
                    ->numeric()
                    ->step(0.0001)
                    ->required(),
//
                TextInput::make('redirect_url')
                    ->label('Redirect URL')
                    ->url()
                    ->required(),
        //         your other fields...

                Forms\Components\FileUpload::make('image')
                    ->image() // marks it as an image
                        ->disk("public")
                    ->directory('product-images') // optional: where images will be stored
                  //  ->imagePreviewHeight('100') // optional
                        ->visibility("public")
                    ->maxSize(5000)
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('product_price'),
                TextColumn::make('product_fee'),
                TextColumn::make('reference_id')
                    ->label('Payment Link')
                    ->formatStateUsing(fn($record) => url("product/payment/{$record->reference_id}"))
                    ->url(fn($record) => url("product/payment/{$record->reference_id}"))
                    ->openUrlInNewTab()
                    ->copyable(fn($record) => url("product/payment/{$record->reference_id}"))
                    ->copyMessage('Link copied!')
                    ->copyableState(fn($record) => url("product/payment/{$record->reference_id}"))
                    ->copyMessageDuration(1500)
                    ->searchable()
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductPaymentLinks::route('/'),
            'create' => Pages\CreateProductPaymentLink::route('/create'),
            'edit' => Pages\EditProductPaymentLink::route('/{record}/edit'),
            'view' => Pages\ViewProductPaymentLink::route('/{record}/view')
        ];
    }
}
