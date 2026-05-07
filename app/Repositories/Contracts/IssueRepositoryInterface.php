<?php

namespace App\Repositories\Contracts;

use App\Enums\IssueCategory;
use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface IssueRepositoryInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection;

    public function find(int $id): \App\Models\Issue;

    public function create(array $data): \App\Models\Issue;

    public function update(int $id, array $data): \App\Models\Issue;

    public function delete(int $id): bool;

    public function paginate(int $perPage = 20): LengthAwarePaginator;

    public function filterByStatus(?IssueStatus $status): self;

    public function filterByCategory(?IssueCategory $category): self;

    public function filterByPriority(?IssuePriority $priority): self;
}