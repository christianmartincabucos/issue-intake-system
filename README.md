# Issue Intake and Smart Summary System

A Laravel-based issue tracking system with AI-powered summary generation.

## Features

- Create, read, update, delete issues
- Filter issues by status, category, and priority
- AI-generated summaries and suggested next actions (via OpenAI)
- Rules-based fallback when AI is unavailable
- Automatic escalation for high-priority and critical issues
- Clean REST API
- Simple web UI for testing

## Tech Stack

- **Backend**: PHP 8.2+, Laravel 11
- **Database**: SQLite (relational, zero-config, perfect for demos)
- **AI**: OpenAI API with rules-based fallback
- **Frontend**: Laravel Blade templates

## Why SQLite?

SQLite was chosen for this demonstration because:
- Zero configuration - no separate server process needed
- Self-contained database file
- Perfect for development and testing
- Easy to switch to PostgreSQL/MySQL in production

## Setup

### Prerequisites

- PHP 8.2 or higher
- Composer
- OpenAI API key (optional - falls back to rules-based summaries)

### Installation

1. Clone the repository and navigate to the project:
```bash
cd issue-intake-system
```

2. Install dependencies:
```bash
composer install
```

3. Create the SQLite database:
```bash
touch database/database.sqlite
```

4. Copy the environment file:
```bash
cp .env.example .env
```

5. Generate the application key:
```bash
php artisan key:generate
```

6. Run migrations:
```bash
php artisan migrate
```

7. (Optional) Configure OpenAI:
   - Get your API key from [OpenAI](https://platform.openai.com/api-keys)
   - Add to `.env`:
   ```
   OPENAI_API_KEY=your-api-key-here
   ```

8. Seed sample data:
```bash
php artisan db:seed
```

9. Start the development server:
```bash
php artisan serve
```

Visit `http://localhost:8000/issues` to use the web UI.

## API Endpoints

### List Issues
```
GET /api/issues
GET /api/issues?status=open
GET /api/issues?priority=high
GET /api/issues?category=bug
```

### Create Issue
```
POST /api/issues
Content-Type: application/json

{
    "title": "Issue title",
    "description": "Issue description",
    "priority": "high",      // optional: low, medium, high, critical
    "category": "bug"        // optional: bug, feature_request, support, infrastructure, security, other
}
```

### View Issue
```
GET /api/issues/{id}
```

### Update Issue
```
PUT /api/issues/{id}
Content-Type: application/json

{
    "title": "Updated title",
    "status": "in_progress"   // optional: open, in_progress, resolved, closed
}
```

### Delete Issue
```
DELETE /api/issues/{id}
```

## AI Summary Generation

When an issue is created or updated, the system automatically generates:

1. **Summary**: A brief description of the issue
2. **Suggested Action**: Recommended next steps for the support team

### With OpenAI API

If `OPENAI_API_KEY` is configured, the system uses GPT-4 for intelligent, context-aware summaries.

### Without OpenAI (Rules-Based Fallback)

The system analyzes the issue text using keyword matching and generates summaries based on:
- Category detection (bug, feature request, support, etc.)
- Priority-based action templates
- Natural language processing for description summarization

## Escalation Rules

Issues are automatically escalated (flagged) when:
- Priority is **Critical** or **High**
- Status is **Open** or **In Progress**

Escalated issues have their `escalated_at` timestamp set.

## Testing

Run the test suite:
```bash
php artisan test
```

Run specific test files:
```bash
php artisan test --filter=IssueApiTest
php artisan test --filter=IssueSummaryServiceTest
```

## Architecture

```
app/
├── Enums/          # Priority, Status, Category enums
├── Http/
│   ├── Controllers/IssueController.php   # API + Web controllers
│   └── Requests/   # Form request validation
├── Models/Issue.php # Eloquent model
└── Services/
    ├── IssueSummaryService.php        # Orchestrator
    ├── OpenAISummaryService.php       # OpenAI integration
    └── RulesBasedSummaryService.php   # Fallback engine
```

## What I Would Improve With More Time

1. **Authentication**: Add user authentication and authorization
2. **Comments/Notes**: Allow adding internal notes to issues
3. **Attachments**: Support file uploads for screenshots
4. **Email Notifications**: Notify users when issues are updated
5. **Better AI Prompting**: Fine-tune prompts for higher quality summaries
6. **Analytics Dashboard**: Track issue metrics and trends
7. **API Rate Limiting**: Protect the API from abuse
8. **Webhooks**: Integrate with external tools like Slack
9. **Database Migrations Path**: Support PostgreSQL/MySQL in production

## License

MIT
