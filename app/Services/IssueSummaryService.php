<?php

namespace App\Services;

use App\Enums\IssuePriority;
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
                try {
                    Log::warning('OpenAI failed, falling back to rules: ' . $e->getMessage());
                } catch (\RuntimeException $logError) {
                    // Log facade not available (e.g., in unit tests)
                }
            }
        }

        return $this->generateWithRules($issue);
    }

    private function generateWithAI(Issue $issue): array
    {
        return [
            'summary' => $this->openAIService->generateSummary(
                $issue->title,
                $issue->description
            ),
            'suggested_action' => $this->openAIService->generateSuggestedAction(
                $issue->title,
                $issue->description,
                $issue->priority
            ),
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