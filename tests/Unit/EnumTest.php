<?php

namespace Tests\Unit;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\IssueCategory;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    public function test_priority_has_correct_weights(): void
    {
        $this->assertEquals(1, IssuePriority::LOW->weight());
        $this->assertEquals(2, IssuePriority::MEDIUM->weight());
        $this->assertEquals(3, IssuePriority::HIGH->weight());
        $this->assertEquals(4, IssuePriority::CRITICAL->weight());
    }

    public function test_priority_labels_are_human_readable(): void
    {
        $this->assertEquals('Low', IssuePriority::LOW->label());
        $this->assertEquals('Critical', IssuePriority::CRITICAL->label());
    }

    public function test_status_labels_are_human_readable(): void
    {
        $this->assertEquals('Open', IssueStatus::OPEN->label());
        $this->assertEquals('In Progress', IssueStatus::IN_PROGRESS->label());
    }

    public function test_category_labels_are_human_readable(): void
    {
        $this->assertEquals('Bug Report', IssueCategory::BUG->label());
        $this->assertEquals('Feature Request', IssueCategory::FEATURE_REQUEST->label());
    }
}
