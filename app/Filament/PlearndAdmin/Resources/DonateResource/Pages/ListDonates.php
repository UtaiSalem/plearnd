<?php

namespace App\Filament\PlearndAdmin\Resources\DonateResource\Pages;

use App\Filament\PlearndAdmin\Resources\DonateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDonates extends ListRecords
{
    protected static string $resource = DonateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
