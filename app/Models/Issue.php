<?php

namespace App\Models;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\IssueCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;


    protected $fillable = [
        'title',
        'description',
        'priority',
        'category',
        'status',
        'summary',
        'suggested_action',
        'escalated_at',
    ];

    protected $casts = [
        'priority' => IssuePriority::class,
        'status' => IssueStatus::class,
        'category' => IssueCategory::class,
        'escalated_at' => 'datetime',
    ];

    public function isEscalated(): bool
    {
        return $this->escalated_at !== null;
    }

    public function needsEscalation(): bool
    {
        if ($this->status === IssueStatus::RESOLVED || $this->status === IssueStatus::CLOSED) {
            return false;
        }

        return $this->priority === IssuePriority::CRITICAL || $this->priority === IssuePriority::HIGH;
    }
}