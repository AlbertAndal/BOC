<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BocOffice extends Model
{
    use HasFactory;

    protected $table = 'boc_offices';
    protected $primaryKey = 'office_id';

    protected $fillable = [
        'office_name',
        'location',
    ];
}

