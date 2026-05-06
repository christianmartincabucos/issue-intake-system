<?php

namespace Tests\Unit;

use App\Enums\IssueCategory;
use App\Enums\IssuePriority;
use App\Services\RulesBasedSummaryService;
use PHPUnit\Framework\TestCase;

class RulesBasedSummaryServiceTest extends TestCase
{
    private RulesBasedSummaryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RulesBasedSummaryService();
    }

    public function test_generates_summary_for_bug_report(): void
    {
        $summary = $this->service->generateSummary(
            'App crashes on login',
            'When I try to login with valid credentials the app crashes'
        );

        $this->assertStringContainsString('Bug', $summary);
    }

    public function test_generates_summary_for_feature_request(): void
    {
        $summary = $this->service->generateSummary(
            'Add dark mode',
            'It would be nice to have a dark mode feature for the application'
        );

        $this->assertStringContainsString('Feature', $summary);
    }

    public function test_generates_action_for_critical_priority(): void
    {
        $action = $this->service->generateSuggestedAction(
            'Production down',
            'The production server is not responding',
            IssuePriority::CRITICAL
        );

        $this->assertStringContainsString('IMMEDIATE', $action);
    }

    public function test_generates_action_for_support_issue(): void
    {
        $action = $this->service->generateSuggestedAction(
            'How do I reset my password?',
            'I forgot my password and need help resetting it',
            IssuePriority::LOW
        );

        $this->assertStringContainsString('documentation', strtolower($action));
    }

    public function test_short_description_uses_title(): void
    {
        $summary = $this->service->generateSummary(
            'Quick question',
            'Hi'
        );

        $this->assertStringContainsString('Quick question', $summary);
    }
}