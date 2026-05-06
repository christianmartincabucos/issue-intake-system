<?php

namespace App\Enums;

enum IssueCategory: string
{
    case BUG = 'bug';
    case FEATURE_REQUEST = 'feature_request';
    case SUPPORT = 'support';
    case INFRASTRUCTURE = 'infrastructure';
    case SECURITY = 'security';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::BUG => 'Bug Report',
            self::FEATURE_REQUEST => 'Feature Request',
            self::SUPPORT => 'General Support',
            self::INFRASTRUCTURE => 'Infrastructure',
            self::SECURITY => 'Security',
            self::OTHER => 'Other',
        };
    }
}
