<?php

namespace App\Repositories;

use App\Enums\IssueCategory;
use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Models\Issue;
use App\Repositories\Contracts\IssueRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class EloquentIssueRepository implements IssueRepositoryInterface
{
    private Builder $query;

    public function __construct()
    {
        $this->query = Issue::query();
    }

    public function all(): Collection
    {
        return $this->query->orderBy('created_at', 'desc')->get();
    }

    public function find(int $id): Issue
    {
        return Issue::findOrFail($id);
    }

    public function create(array $data): Issue
    {
        return Issue::create($data);
    }

    public function update(int $id, array $data): Issue
    {
        $issue = $this->find($id);
        $issue->update($data);
        return $issue;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }

    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return $this->query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function filterByStatus(?IssueStatus $status): self
    {
        if ($status !== null) {
            $this->query->where('status', $status);
        }
        return $this;
    }

    public function filterByCategory(?IssueCategory $category): self
    {
        if ($category !== null) {
            $this->query->where('category', $category);
        }
        return $this;
    }

    public function filterByPriority(?IssuePriority $priority): self
    {
        if ($priority !== null) {
            $this->query->where('priority', $priority);
        }
        return $this;
    }
}
