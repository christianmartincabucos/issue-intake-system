<?php

namespace App\Services;

use App\Enums\IssueCategory;
use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Models\Issue;
use App\Repositories\Contracts\IssueRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IssueService
{
    public function __construct(
        private IssueRepositoryInterface $repository,
        private IssueSummaryService $summaryService
    ) {}

    public function getAllIssues(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->all();
    }

    public function getIssue(int $id): Issue
    {
        return $this->repository->find($id);
    }

    public function listIssues(
        ?IssueStatus $status = null,
        ?IssueCategory $category = null,
        ?IssuePriority $priority = null,
        int $perPage = 20
    ): LengthAwarePaginator {
        return $this->repository
            ->filterByStatus($status)
            ->filterByCategory($category)
            ->filterByPriority($priority)
            ->paginate($perPage);
    }

    public function createIssue(array $data): Issue
    {
        if (!isset($data['status'])) {
            $data['status'] = IssueStatus::OPEN;
        }

        $issue = $this->repository->create($data);

        $this->processIssueSummary($issue);
        $this->handleEscalation($issue);

        return $issue->fresh();
    }

    public function updateIssue(int $id, array $data): Issue
    {
        $issue = $this->repository->update($id, $data);

        $this->handleEscalation($issue);

        if ($this->shouldRegenerateSummary($data)) {
            $this->processIssueSummary($issue);
            $issue = $issue->fresh();
        }

        return $issue;
    }

    public function deleteIssue(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function updateIssueStatus(int $id, IssueStatus $status): Issue
    {
        return $this->repository->update($id, ['status' => $status]);
    }

    private function processIssueSummary(Issue $issue): void
    {
        $summaryData = $this->summaryService->generateForIssue($issue);
        $issue->update($summaryData);
    }

    private function handleEscalation(Issue $issue): void
    {
        if ($issue->needsEscalation() && !$issue->isEscalated()) {
            $issue->update(['escalated_at' => now()]);
        }
    }

    private function shouldRegenerateSummary(array $data): bool
    {
        return isset($data['title']) || isset($data['description']);
    }
}