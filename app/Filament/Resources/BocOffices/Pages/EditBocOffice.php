<?php

namespace App\Filament\Resources\BocOffices\Pages;

use App\Filament\Resources\BocOffices\BocOfficeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBocOffice extends EditRecord
{
    protected static string $resource = BocOfficeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
