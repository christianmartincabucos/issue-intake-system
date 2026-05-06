<?php

namespace Tests\Unit;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\IssueCategory;
use App\Models\Issue;
use App\Services\IssueSummaryService;
use App\Services\OpenAISummaryService;
use App\Services\RulesBasedSummaryService;
use Mockery;
use PHPUnit\Framework\TestCase;

class IssueSummaryServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_uses_ai_when_available(): void
    {
        $openAI = Mockery::mock(OpenAISummaryService::class);
        $openAI->shouldReceive('isAvailable')->andReturn(true);
        $openAI->shouldReceive('generateSummary')->andReturn('AI summary');
        $openAI->shouldReceive('generateSuggestedAction')->andReturn('AI action');

        $rules = Mockery::mock(RulesBasedSummaryService::class);

        $service = new IssueSummaryService($openAI, $rules);

        $issue = new Issue();
        $issue->title = 'Test issue';
        $issue->description = 'Test description';
        $issue->priority = IssuePriority::HIGH;
        $issue->status = IssueStatus::OPEN;
        $issue->category = IssueCategory::BUG;

        $result = $service->generateForIssue($issue);

        $this->assertEquals('AI summary', $result['summary']);
        $this->assertEquals('AI action', $result['suggested_action']);
        $this->assertTrue($result['ai_generated']);
    }

    public function test_falls_back_to_rules_when_ai_unavailable(): void
    {
        $openAI = Mockery::mock(OpenAISummaryService::class);
        $openAI->shouldReceive('isAvailable')->andReturn(false);

        $rules = Mockery::mock(RulesBasedSummaryService::class);
        $rules->shouldReceive('generateSummary')->andReturn('Rules summary');
        $rules->shouldReceive('generateSuggestedAction')->andReturn('Rules action');

        $service = new IssueSummaryService($openAI, $rules);

        $issue = new Issue();
        $issue->title = 'Test issue';
        $issue->description = 'Test description';
        $issue->priority = IssuePriority::HIGH;
        $issue->status = IssueStatus::OPEN;
        $issue->category = IssueCategory::BUG;

        $result = $service->generateForIssue($issue);

        $this->assertEquals('Rules summary', $result['summary']);
        $this->assertEquals('Rules action', $result['suggested_action']);
        $this->assertFalse($result['ai_generated']);
    }

    public function test_falls_back_to_rules_when_ai_fails(): void
    {
        $openAI = Mockery::mock(OpenAISummaryService::class);
        $openAI->shouldReceive('isAvailable')->andReturn(true);
        $openAI->shouldReceive('generateSummary')->andThrow(new \RuntimeException('API error'));

        $rules = Mockery::mock(RulesBasedSummaryService::class);
        $rules->shouldReceive('generateSummary')->andReturn('Fallback summary');
        $rules->shouldReceive('generateSuggestedAction')->andReturn('Fallback action');

        $service = new IssueSummaryService($openAI, $rules);

        $issue = new Issue();
        $issue->title = 'Test issue';
        $issue->description = 'Test description';
        $issue->priority = IssuePriority::HIGH;
        $issue->status = IssueStatus::OPEN;
        $issue->category = IssueCategory::BUG;

        $result = $service->generateForIssue($issue);

        $this->assertEquals('Fallback summary', $result['summary']);
        $this->assertFalse($result['ai_generated']);
    }
}