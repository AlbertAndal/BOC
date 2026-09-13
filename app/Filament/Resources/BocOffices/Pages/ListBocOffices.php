<?php

namespace App\Filament\Resources\BocOffices\Pages;

use App\Filament\Resources\BocOffices\BocOfficeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBocOffices extends ListRecords
{
    protected static string $resource = BocOfficeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
