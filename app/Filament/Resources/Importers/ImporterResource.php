<?php

namespace App\Filament\Resources\Importers;

use App\Filament\Resources\Importers\Pages\CreateImporter;
use App\Filament\Resources\Importers\Pages\EditImporter;
use App\Filament\Resources\Importers\Pages\ListImporters;
use App\Filament\Resources\Importers\Schemas\ImporterForm;
use App\Filament\Resources\Importers\Tables\ImportersTable;
use App\Models\Importer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class ImporterResource extends Resource
{
    protected static ?string $model = Importer::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static string|UnitEnum|null $navigationGroup = 'Master Records';

    protected static ?string $recordTitleAttribute = 'importer_name';

    public static function form(Schema $schema): Schema
    {
        return ImporterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ImportersTable::configure($table);
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
            'index' => ListImporters::route('/'),
            'create' => CreateImporter::route('/create'),
            'edit' => EditImporter::route('/{record}/edit'),
        ];
    }
}
