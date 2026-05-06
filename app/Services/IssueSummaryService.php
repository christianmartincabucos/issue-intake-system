<?php

namespace App\Services;

use App\Models\Issue;
use Illuminate\Support\Facades\Log;

class IssueSummaryService
{
    private OpenAISummaryService $openAIService;
    private RulesBasedSummaryService $rulesService;

    public function __construct(
        OpenAISummaryService $openAIService,
        RulesBasedSummaryService $rulesService
    ) {
        $this->openAIService = $openAIService;
        $this->rulesService = $rulesService;
    }

    public function generateForIssue(Issue $issue): array
    {
        $useAI = $this->openAIService->isAvailable();

        if ($useAI) {
            try {
                return $this->generateWithAI($issue);
            } catch (\Exception $e) {
                Log::warning('OpenAI failed, falling back to rules: ' . $e->getMessage());
            }
        }

        return $this->generateWithRules($issue);
    }

    private function generateWithAI(Issue $issue): array
    {
        $summary = null;
        $action = null;

        try {
            $summary = $this->openAIService->generateSummary($issue->title, $issue->description);
        } catch (\Exception $e) {
            Log::warning('OpenAI summary generation failed: ' . $e->getMessage());
        }

        try {
            $action = $this->openAIService->generateSuggestedAction($issue->title, $issue->description, $issue->priority);
        } catch (\Exception $e) {
            Log::warning('OpenAI suggested action generation failed: ' . $e->getMessage());
        }

        // If either failed, fall back to rules
        if ($summary === null || $action === null) {
            return $this->generateWithRules($issue);
        }

        return [
            'summary' => $summary,
            'suggested_action' => $action,
            'ai_generated' => true,
        ];
    }

    private function generateWithRules(Issue $issue): array
    {
        return [
            'summary' => $this->rulesService->generateSummary(
                $issue->title,
                $issue->description
            ),
            'suggested_action' => $this->rulesService->generateSuggestedAction(
                $issue->title,
                $issue->description,
                $issue->priority
            ),
            'ai_generated' => false,
        ];
    }
}