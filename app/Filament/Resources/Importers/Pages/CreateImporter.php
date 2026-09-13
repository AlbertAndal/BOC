<?php

namespace App\Filament\Resources\Importers\Pages;

use App\Filament\Resources\Importers\ImporterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateImporter extends CreateRecord
{
    protected static string $resource = ImporterResource::class;
}
