<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PawapayAccountResource\Pages;
use App\Filament\Resources\PawapayAccountResource\RelationManagers;
use App\Models\PawapayAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class PawapayAccountResource extends Resource
{
    protected static ?string $model = PawapayAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('api_key')->required(),
                Forms\Components\Toggle::make('is_default'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('api_key')->limit(20),
                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('set_default')
                    ->label('Set as Default')
                    ->visible(fn ($record) => !$record->is_default)
                    ->action(function (PawapayAccount $record) {
                        // Set all others to false
                        PawapayAccount::where('id', '!=', $record->id)
                            ->update(['is_default' => false]);

                        // Set this one to true
                        $record->is_default = true;
                        $record->save();

                        Notification::make()
                            ->title('Default account set successfully.')
                            ->success()
                            ->send();

                    })
                    ->requiresConfirmation()
                    ->color('primary')
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
            'index' => Pages\ListPawapayAccounts::route('/'),
            'create' => Pages\CreatePawapayAccount::route('/create'),
            'edit' => Pages\EditPawapayAccount::route('/{record}/edit'),
        ];
    }
}
