<?php

namespace App\Filament\Resources\Importers\Pages;

use App\Filament\Resources\Importers\ImporterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListImporters extends ListRecords
{
    protected static string $resource = ImporterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
