<?php

namespace Tests\Unit;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\IssueCategory;
use App\Models\Issue;
use PHPUnit\Framework\TestCase;

class IssueModelTest extends TestCase
{
    public function test_issue_needs_escalation_for_critical_priority(): void
    {
        $issue = new Issue();
        $issue->priority = IssuePriority::CRITICAL;
        $issue->status = IssueStatus::OPEN;

        $this->assertTrue($issue->needsEscalation());
    }

    public function test_issue_needs_escalation_for_high_priority(): void
    {
        $issue = new Issue();
        $issue->priority = IssuePriority::HIGH;
        $issue->status = IssueStatus::OPEN;

        $this->assertTrue($issue->needsEscalation());
    }

    public function test_issue_does_not_need_escalation_for_low_priority(): void
    {
        $issue = new Issue();
        $issue->priority = IssuePriority::LOW;
        $issue->status = IssueStatus::OPEN;

        $this->assertFalse($issue->needsEscalation());
    }

    public function test_issue_does_not_need_escalation_when_resolved(): void
    {
        $issue = new Issue();
        $issue->priority = IssuePriority::CRITICAL;
        $issue->status = IssueStatus::RESOLVED;

        $this->assertFalse($issue->needsEscalation());
    }

    public function test_issue_is_not_escalated_by_default(): void
    {
        $issue = new Issue();

        $this->assertFalse($issue->isEscalated());
    }
}