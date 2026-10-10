<?php

namespace App\Services;

use App\Enums\IssueStatus;
use App\Models\Issue;

class IssueWorkflowService
{
    /**
     * 10-Step BOC Issue Resolution Workflow Definitions
     */
    public static function getSteps(): array
    {
        return [
            1 => [
                'number' => 1,
                'name' => 'Concern Submission',
                'description' => 'Customs Broker submits the client concern with initial entry details and supporting documents.',
                'status' => IssueStatus::New,
                'actor' => 'Customs Broker',
            ],
            2 => [
                'number' => 2,
                'name' => 'Intake Verification & Document Check',
                'description' => 'Administrator reviews documents for completeness and verifies client authorization.',
                'status' => IssueStatus::UnderReview,
                'actor' => 'Administrator',
            ],
            3 => [
                'number' => 3,
                'name' => 'Accounts Manager Assignment',
                'description' => 'Administrator designates and assigns the case to an Accounts Manager.',
                'status' => IssueStatus::Assigned,
                'actor' => 'Administrator',
            ],
            4 => [
                'number' => 4,
                'name' => 'Preliminary Technical Assessment',
                'description' => 'Accounts Manager reviews tariff classifications, valuation issues, or port alerts.',
                'status' => IssueStatus::InProgress,
                'actor' => 'Accounts Manager',
            ],
            5 => [
                'number' => 5,
                'name' => 'Client Clarification & Proof Request',
                'description' => 'Manager requests additional documentation, permits, or clarification from client/broker.',
                'status' => IssueStatus::PendingClient,
                'actor' => 'Accounts Manager',
            ],
            6 => [
                'number' => 6,
                'name' => 'Client Compliance Submission',
                'description' => 'Broker/client provides requested documents, explanations, or compliance certificates.',
                'status' => IssueStatus::InProgress,
                'actor' => 'Customs Broker',
            ],
            7 => [
                'number' => 7,
                'name' => 'BOC Office Endorsement',
                'description' => 'Manager coordinates and formally endorses the issue to the relevant BOC operating office.',
                'status' => IssueStatus::PendingBocCoordination,
                'actor' => 'Accounts Manager',
            ],
            8 => [
                'number' => 8,
                'name' => 'Office Findings & Evaluation',
                'description' => 'Receiving BOC office returns evaluation report, inspection results, or reassessment finding.',
                'status' => IssueStatus::InProgress,
                'actor' => 'Accounts Manager',
            ],
            9 => [
                'number' => 9,
                'name' => 'Resolution & Clearance Determination',
                'description' => 'Settlement terms, valuation adjustment, or shipment clearance order formulated and finalized.',
                'status' => IssueStatus::Resolved,
                'actor' => 'Accounts Manager / Admin',
            ],
            10 => [
                'number' => 10,
                'name' => 'Client Acceptance & Audit Closure',
                'description' => 'Client confirms resolution, audit logging finalized, and issue closed.',
                'status' => IssueStatus::Closed,
                'actor' => 'Administrator',
            ],
        ];
    }

    /**
     * Transition an issue to a workflow step
     */
    public static function transition(
        Issue $issue,
        int $stepNumber,
        ?string $coordinatedOffice = null,
        ?string $remarks = null,
        ?int $assignedManagerId = null
    ): Issue {
        $steps = static::getSteps();
        $step = $steps[$stepNumber] ?? null;

        if (! $step) {
            throw new \InvalidArgumentException("Invalid workflow step: {$stepNumber}");
        }

        $issue->current_coordinated_office = $coordinatedOffice;
        $issue->current_action_remarks = $remarks;
        $issue->status_id = $step['status'];

        if ($assignedManagerId) {
            $issue->assigned_manager = $assignedManagerId;
        }

        if ($step['status'] === IssueStatus::Resolved) {
            $issue->date_resolved = now();
        }

        if ($remarks) {
            $issue->remarks = $remarks;
        }

        $issue->save();

        return $issue;
    }
}
