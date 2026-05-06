<?php

namespace Database\Seeders;

use App\Enums\IssueCategory;
use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Models\Issue;
use Illuminate\Database\Seeder;

class IssueSeeder extends Seeder
{
    public function run(): void
    {
        $issues = [
            [
                'title' => 'Production server responding slowly',
                'description' => 'Multiple users are reporting that the production server is responding very slowly. Response times have increased from 200ms to over 5 seconds. This started happening after the latest deployment.',
                'priority' => IssuePriority::CRITICAL,
                'category' => IssueCategory::INFRASTRUCTURE,
                'status' => IssueStatus::OPEN,
            ],
            [
                'title' => 'User cannot login with Google OAuth',
                'description' => 'A user reported that they cannot login using their Google account. They get an error message saying "Authentication failed". This affects only Google OAuth users.',
                'priority' => IssuePriority::HIGH,
                'category' => IssueCategory::BUG,
                'status' => IssueStatus::IN_PROGRESS,
            ],
            [
                'title' => 'Request for dark mode feature',
                'description' => 'Several users have requested a dark mode option for the application. This would help reduce eye strain during nighttime usage and save battery on OLED screens.',
                'priority' => IssuePriority::LOW,
                'category' => IssueCategory::FEATURE_REQUEST,
                'status' => IssueStatus::OPEN,
            ],
            [
                'title' => 'How to reset two-factor authentication?',
                'description' => 'A user is asking how to reset their two-factor authentication after they lost their phone. They need step-by-step instructions.',
                'priority' => IssuePriority::MEDIUM,
                'category' => IssueCategory::SUPPORT,
                'status' => IssueStatus::RESOLVED,
            ],
            [
                'title' => 'Potential XSS vulnerability in comment section',
                'description' => 'Security researcher reported a potential XSS vulnerability in the comment section. HTML tags in comments are not being properly sanitized.',
                'priority' => IssuePriority::HIGH,
                'category' => IssueCategory::SECURITY,
                'status' => IssueStatus::OPEN,
            ],
            [
                'title' => 'Export to CSV not working',
                'description' => 'When clicking the export button, the CSV file downloads but all values are empty. This is a regression introduced in version 2.3.1.',
                'priority' => IssuePriority::MEDIUM,
                'category' => IssueCategory::BUG,
                'status' => IssueStatus::OPEN,
            ],
        ];

        foreach ($issues as $issueData) {
            $issue = Issue::create($issueData);

            $summaryService = app(\App\Services\IssueSummaryService::class);
            $summaryData = $summaryService->generateForIssue($issue);
            $issue->update($summaryData);

            if ($issue->needsEscalation()) {
                $issue->update(['escalated_at' => now()]);
            }
        }
    }
}
