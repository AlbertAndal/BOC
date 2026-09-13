<?php

namespace App\Filament\Resources\Importers\Pages;

use App\Filament\Resources\Importers\ImporterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditImporter extends EditRecord
{
    protected static string $resource = ImporterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
