<?php

namespace App\Services;

use App\Enums\IssuePriority;
use OpenAI;

class OpenAISummaryService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', env('OPENAI_API_KEY', ''));
        $this->model = config('services.openai.model', 'gpt-4o-mini');
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    public function generateSummary(string $title, string $description): string
    {
        $prompt = $this->buildSummaryPrompt($title, $description);

        try {
            $response = $this->callOpenAI($prompt);
            return $this->parseSummaryResponse($response);
        } catch (\Exception $e) {
            throw new \RuntimeException('OpenAI API call failed: ' . $e->getMessage());
        }
    }

    public function generateSuggestedAction(string $title, string $description, IssuePriority $priority): string
    {
        $prompt = $this->buildActionPrompt($title, $description, $priority);

        try {
            $response = $this->callOpenAI($prompt);
            return $this->parseActionResponse($response);
        } catch (\Exception $e) {
            throw new \RuntimeException('OpenAI API call failed: ' . $e->getMessage());
        }
    }

    private function callOpenAI(string $prompt): string
    {
        $client = OpenAI::client($this->apiKey);

        $result = $client->chat()->create([
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful support ticket analyst. Keep responses concise and actionable.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 150,
            'temperature' => 0.3,
        ]);

        return $result->choices[0]->message->content;
    }

    private function buildSummaryPrompt(string $title, string $description): string
    {
        return "Summarize the following support ticket in 1-2 sentences. Focus on the core problem.\n\nTitle: {$title}\nDescription: {$description}";
    }

    private function buildActionPrompt(string $title, string $description, IssuePriority $priority): string
    {
        $priorityLabel = ucfirst($priority->value);
        return "Based on this support ticket, suggest the next action the support team should take. Be specific and actionable.\n\nTitle: {$title}\nDescription: {$description}\nPriority: {$priorityLabel}";
    }

    private function parseSummaryResponse(string $response): string
    {
        $summary = trim($response);
        if (strlen($summary) > 200) {
            $summary = substr($summary, 0, 197) . '...';
        }
        return $summary;
    }

    private function parseActionResponse(string $response): string
    {
        $action = trim($response);
        if (strlen($action) > 300) {
            $action = substr($action, 0, 297) . '...';
        }
        return $action;
    }
}