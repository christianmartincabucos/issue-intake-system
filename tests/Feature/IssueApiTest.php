<?php

namespace Tests\Feature;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\IssueCategory;
use App\Models\Issue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssueApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_issue(): void
    {
        $response = $this->postJson('/api/issues', [
            'title' => 'Test issue',
            'description' => 'Test description',
            'priority' => 'high',
            'category' => 'bug',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'title', 'description', 'priority', 'category', 'status', 'summary', 'suggested_action'],
                'ai_generated',
            ]);

        $this->assertDatabaseHas('issues', ['title' => 'Test issue']);
    }

    public function test_created_issue_generates_summary(): void
    {
        $response = $this->postJson('/api/issues', [
            'title' => 'Application crashes',
            'description' => 'The application crashes when clicking the submit button',
            'priority' => 'critical',
            'category' => 'bug',
        ]);

        $response->assertStatus(201);
        $this->assertNotEmpty($response->json('data.summary'));
        $this->assertNotEmpty($response->json('data.suggested_action'));
    }

    public function test_created_high_priority_issue_is_escalated(): void
    {
        $response = $this->postJson('/api/issues', [
            'title' => 'Production down',
            'description' => 'Production server is not responding',
            'priority' => 'critical',
        ]);

        $response->assertStatus(201);
        $this->assertNotNull($response->json('data.escalated_at'));
    }

    public function test_can_list_issues(): void
    {
        Issue::factory()->count(3)->create();

        $response = $this->getJson('/api/issues');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_filter_issues_by_status(): void
    {
        Issue::factory()->create(['status' => IssueStatus::OPEN]);
        Issue::factory()->create(['status' => IssueStatus::CLOSED]);

        $response = $this->getJson('/api/issues?status=open');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_filter_issues_by_priority(): void
    {
        Issue::factory()->create(['priority' => IssuePriority::HIGH]);
        Issue::factory()->create(['priority' => IssuePriority::LOW]);

        $response = $this->getJson('/api/issues?priority=high');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_filter_issues_by_category(): void
    {
        Issue::factory()->create(['category' => IssueCategory::BUG]);
        Issue::factory()->create(['category' => IssueCategory::SUPPORT]);

        $response = $this->getJson('/api/issues?category=bug');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_view_single_issue(): void
    {
        $issue = Issue::factory()->create();

        $response = $this->getJson("/api/issues/{$issue->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $issue->id);
    }

    public function test_can_update_issue(): void
    {
        $issue = Issue::factory()->create(['title' => 'Old title']);

        $response = $this->putJson("/api/issues/{$issue->id}", [
            'title' => 'New title',
            'status' => 'in_progress',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'New title')
            ->assertJsonPath('data.status', 'in_progress');
    }

    public function test_can_delete_issue(): void
    {
        $issue = Issue::factory()->create();

        $response = $this->deleteJson("/api/issues/{$issue->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('issues', ['id' => $issue->id]);
    }

    public function test_view_returns_404_for_missing_issue(): void
    {
        $response = $this->getJson('/api/issues/999');

        $response->assertStatus(404);
    }

    public function test_update_returns_404_for_missing_issue(): void
    {
        $response = $this->putJson('/api/issues/999', [
            'title' => 'New title',
        ]);

        $response->assertStatus(404);
    }
}
