<?php

namespace App\Services;

use App\Enums\IssueCategory;
use App\Enums\IssuePriority;

class RulesBasedSummaryService
{
    private const CATEGORY_KEYWORDS = [
        'bug' => ['error', 'bug', 'crash', 'broken', 'fails', 'not working', 'issue'],
        'feature_request' => ['would be nice', 'should have', 'add', 'feature', 'request', 'enhance'],
        'support' => ['help', 'question', 'how to', 'confused', 'understand', 'need assistance'],
        'infrastructure' => ['server', 'database', 'performance', 'slow', 'timeout', 'infrastructure'],
        'security' => ['security', 'vulnerability', 'exploit', 'hack', 'breach', 'unauthorized'],
    ];

    private const ACTION_TEMPLATES = [
        'bug' => 'Investigate and reproduce the bug. Create a fix and test thoroughly before deploying.',
        'feature_request' => 'Review the feature request with the product team. Add to backlog if approved.',
        'support' => 'Provide detailed documentation or link to relevant resources. Follow up if unresolved.',
        'infrastructure' => 'Review system metrics and logs. Scale resources or optimize queries as needed.',
        'security' => 'Escalate to the security team immediately. Do not discuss publicly.',
        'other' => 'Review the issue description and determine the appropriate team to handle it.',
    ];

    public function generateSummary(string $title, string $description): string
    {
        $category = $this->detectCategory($title, $description);
        $wordCount = str_word_count($description);

        $summary = "[" . ucfirst($category->value) . "] ";

        if ($wordCount < 20) {
            $summary .= "Brief issue reported: " . $title;
        } elseif ($wordCount < 100) {
            $summary .= "Issue: " . $title . ". " . $this->extractKeyPoint($description);
        } else {
            $summary .= $this->summarizeLongDescription($title, $description);
        }

        return $summary;
    }

    public function generateSuggestedAction(string $title, string $description, IssuePriority $priority): string
    {
        $category = $this->detectCategory($title, $description);
        $action = self::ACTION_TEMPLATES[$category->value];

        if ($priority === IssuePriority::CRITICAL) {
            return "IMMEDIATE ACTION REQUIRED. " . $action;
        }

        if ($priority === IssuePriority::HIGH) {
            return "High priority. " . $action;
        }

        return $action;
    }

    private function detectCategory(string $title, string $description): IssueCategory
    {
        $text = strtolower($title . ' ' . $description);

        $scores = [];
        foreach (self::CATEGORY_KEYWORDS as $category => $keywords) {
            $scores[$category] = 0;
            foreach ($keywords as $keyword) {
                if (str_contains($text, $keyword)) {
                    $scores[$category]++;
                }
            }
        }

        $maxScore = max($scores);
        if ($maxScore === 0) {
            return IssueCategory::OTHER;
        }

        $bestMatch = array_keys($scores, $maxScore)[0];
        return IssueCategory::from($bestMatch);
    }

    private function extractKeyPoint(string $description): string
    {
        $sentences = preg_split('/[.!?]+/', $description);
        $sentences = array_filter(array_map('trim', $sentences));

        return $sentences[0] ?? 'No description provided';
    }

    private function summarizeLongDescription(string $title, string $description): string
    {
        $sentences = preg_split('/[.!?]+/', $description);
        $sentences = array_filter(array_map('trim', $sentences));

        if (count($sentences) <= 3) {
            return implode('. ', $sentences) . '.';
        }

        return implode('. ', array_slice($sentences, 0, 3)) . '...';
    }
}