<?php

namespace App\Models;

use App\Enums\IssueStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Issue extends Model
{
    use HasFactory;

    protected $table = 'issues';
    protected $primaryKey = 'issue_id';

    public ?string $current_coordinated_office = null;
    public ?string $current_action_remarks = null;

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

        // 1. Log Initial Concern Submission into issue_actions
        static::created(function (Issue $issue) {
            $user = auth()->user() ?? User::find($issue->created_by);
            $roleName = $user?->role?->role_name ?? 'Customs Broker';
            $userName = $user?->full_name ?? 'User #' . $issue->created_by;
            $timeStr = now()->format('F j, Y - g:i A');

            IssueAction::create([
                'issue_id' => $issue->issue_id,
                'action_taken' => "{$timeStr}: Concern submitted by {$roleName} ({$userName})",
                'coordinated_office' => null,
                'action_date' => now()->toDateString(),
                'performed_by' => $user?->user_id ?? $issue->created_by,
                'remarks' => 'Initial concern submitted: ' . Str::limit($issue->concern, 120),
            ]);
        });

        // 2. Log Updates into issue_actions audit trail
        static::updated(function (Issue $issue) {
            $dirty = $issue->getDirty();
            unset($dirty['updated_at']);
            if (empty($dirty)) {
                return;
            }

            $user = auth()->user() ?? User::find($issue->created_by);
            $roleName = $user?->role?->role_name ?? 'User';
            $userName = $user?->full_name ?? 'System';
            $timeStr = now()->format('F j, Y - g:i A');

            $actions = [];

            if ($issue->wasChanged('status_id')) {
                $oldStatusRaw = $issue->getOriginal('status_id');
                $oldStatus = $oldStatusRaw instanceof IssueStatus ? $oldStatusRaw : IssueStatus::tryFrom((int)$oldStatusRaw);
                $newStatus = $issue->status_id;
                $oldName = $oldStatus?->getLabel() ?? 'Unknown';
                $newName = $newStatus?->getLabel() ?? 'Unknown';
                $actions[] = "Status changed from '{$oldName}' to '{$newName}'";
            }

            if ($issue->wasChanged('assigned_manager')) {
                $manager = User::find($issue->assigned_manager);
                $managerName = $manager ? $manager->full_name : 'None';
                $actions[] = "Assigned to Accounts Manager {$managerName}";
            }

            if ($issue->wasChanged('date_resolved') && filled($issue->date_resolved)) {
                $actions[] = "Issue marked as Resolved";
            }

            if ($issue->wasChanged('attachments')) {
                $actions[] = "Attachments / documents updated";
            }

            if (empty($actions)) {
                $actions[] = "Issue details updated";
            }

            $actionSummary = implode(', ', $actions);

            IssueAction::create([
                'issue_id' => $issue->issue_id,
                'action_taken' => "{$timeStr}: {$actionSummary} by {$roleName} ({$userName})",
                'coordinated_office' => $issue->current_coordinated_office,
                'action_date' => now()->toDateString(),
                'performed_by' => $user?->user_id ?? $issue->created_by,
                'remarks' => $issue->current_action_remarks ?? ($issue->wasChanged('remarks') ? $issue->remarks : null),
            ]);
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
        return $this->hasMany(IssueAction::class, 'issue_id', 'issue_id')->orderBy('action_id', 'desc');
    }
}
