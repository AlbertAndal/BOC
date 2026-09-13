<?php

namespace App\Filament\Resources\BocOffices\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BocOfficeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('office_name')
                    ->required(),
                TextInput::make('location'),
            ]);
    }
}
