<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Importer extends Model
{
    use HasFactory;

    protected $table = 'importers';

    protected $primaryKey = 'importer_id';

    protected $fillable = [
        'importer_name',
        'company',
        'contact_information',
    ];

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class, 'importer_id', 'importer_id');
    }
}
