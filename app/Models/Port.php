<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Port extends Model
{
    use HasFactory;

    protected $table = 'ports';
    protected $primaryKey = 'port_id';

    protected $fillable = [
        'port_name',
        'location',
    ];

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class, 'port_id', 'port_id');
    }
}

