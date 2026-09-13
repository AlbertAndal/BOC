<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum IssueStatus: int implements HasLabel, HasColor
{
    case New = 1;
    case UnderReview = 2;
    case Assigned = 3;
    case InProgress = 4;
    case PendingClient = 5;
    case PendingBocCoordination = 6;
    case Resolved = 7;
    case Closed = 8;
    case Cancelled = 9;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::New => 'New',
            self::UnderReview => 'Under Review',
            self::Assigned => 'Assigned',
            self::InProgress => 'In Progress',
            self::PendingClient => 'Pending Client',
            self::PendingBocCoordination => 'Pending BOC Coordination',
            self::Resolved => 'Resolved',
            self::Closed => 'Closed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::New => 'info',
            self::UnderReview => 'warning',
            self::Assigned => 'primary',
            self::InProgress => 'sky',
            self::PendingClient => 'purple',
            self::PendingBocCoordination => 'amber',
            self::Resolved => 'success',
            self::Closed => 'gray',
            self::Cancelled => 'danger',
        };
    }
}
