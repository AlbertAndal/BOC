<?php

namespace App\Filament\Resources\Importers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ImporterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('importer_name')
                    ->required(),
                TextInput::make('company'),
                Textarea::make('contact_information')
                    ->columnSpanFull(),
            ]);
    }
}
