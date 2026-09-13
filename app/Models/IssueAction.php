<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueAction extends Model
{
    use HasFactory;

    protected $table = 'issue_actions';
    protected $primaryKey = 'action_id';

    protected $fillable = [
        'issue_id',
        'action_taken',
        'coordinated_office',
        'action_date',
        'performed_by',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'action_date' => 'date',
        ];
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'issue_id', 'issue_id');
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by', 'user_id');
    }
}

