<?php

namespace App\Filament\PlearndAdmin\Resources;

use App\Filament\PlearndAdmin\Resources\DonateResource\Pages;
use App\Filament\PlearndAdmin\Resources\DonateResource\RelationManagers;
use App\Models\Donate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DonateResource extends Resource
{
    protected static ?string $model = Donate::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListDonates::route('/'),
            'create' => Pages\CreateDonate::route('/create'),
            'edit' => Pages\EditDonate::route('/{record}/edit'),
        ];
    }
}
