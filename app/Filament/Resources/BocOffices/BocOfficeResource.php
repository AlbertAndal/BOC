<?php

namespace App\Filament\Resources\BocOffices;

use App\Filament\Resources\BocOffices\Pages\CreateBocOffice;
use App\Filament\Resources\BocOffices\Pages\EditBocOffice;
use App\Filament\Resources\BocOffices\Pages\ListBocOffices;
use App\Filament\Resources\BocOffices\Schemas\BocOfficeForm;
use App\Filament\Resources\BocOffices\Tables\BocOfficesTable;
use App\Models\BocOffice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class BocOfficeResource extends Resource
{
    protected static ?string $model = BocOffice::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|UnitEnum|null $navigationGroup = 'Master Records';

    protected static ?string $navigationLabel = 'BOC Offices';

    protected static ?string $recordTitleAttribute = 'office_name';

    public static function form(Schema $schema): Schema
    {
        return BocOfficeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BocOfficesTable::configure($table);
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
            'index' => ListBocOffices::route('/'),
            'create' => CreateBocOffice::route('/create'),
            'edit' => EditBocOffice::route('/{record}/edit'),
        ];
    }
}
