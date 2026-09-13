<?php

namespace App\Models;

use App\Enums\IssueStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Issue extends Model
{
    use HasFactory;

    protected $table = 'issues';
    protected $primaryKey = 'issue_id';

    protected $fillable = [
        'issue_number',
        'date_received',
        'importer_id',
        'port_id',
        'concern',
        'attachments',
        'benefits_availed',
        'assigned_manager',
        'status_id',
        'date_resolved',
        'remarks',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_received' => 'date',
            'date_resolved' => 'datetime',
            'status_id' => IssueStatus::class,
            'attachments' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Issue $issue) {
            if (empty($issue->issue_number)) {
                $issue->issue_number = static::generateIssueNumber();
            }

            if (empty($issue->status_id)) {
                $issue->status_id = IssueStatus::New;
            }

            if (empty($issue->date_received)) {
                $issue->date_received = now()->toDateString();
            }

            if (empty($issue->created_by) && auth()->check()) {
                $issue->created_by = auth()->id();
            }
        });
    }

    /**
     * Auto-generate a unique barcode/issue number like TIO-08-001
     */
    public static function generateIssueNumber(): string
    {
        $prefix = 'TIO-' . date('m') . '-';
        $lastIssue = static::where('issue_number', 'LIKE', $prefix . '%')
            ->orderBy('issue_id', 'desc')
            ->first();

        if ($lastIssue) {
            $parts = explode('-', $lastIssue->issue_number);
            $sequence = intval(end($parts)) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . str_pad((string)$sequence, 3, '0', STR_PAD_LEFT);
    }

    public function importer(): BelongsTo
    {
        return $this->belongsTo(Importer::class, 'importer_id', 'importer_id');
    }

    public function port(): BelongsTo
    {
        return $this->belongsTo(Port::class, 'port_id', 'port_id');
    }

    public function assignedManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_manager', 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(IssueAction::class, 'issue_id', 'issue_id');
    }
}
