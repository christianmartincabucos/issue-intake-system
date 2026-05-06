<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue #{{ $issue->id }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        h1 { color: #333; }
        .btn { padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; display: inline-block; }
        .btn:hover { background: #0056b3; }
        .btn-secondary { background: #6c757d; }
        .btn-success { background: #28a745; }
        .btn-danger { background: #dc3545; }
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .field { margin-bottom: 15px; }
        .field-label { color: #666; font-size: 12px; text-transform: uppercase; margin-bottom: 5px; }
        .field-value { color: #333; font-size: 16px; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; display: inline-block; }
        .badge-critical { background: #dc3545; color: white; }
        .badge-high { background: #fd7e14; color: white; }
        .badge-medium { background: #ffc107; color: #333; }
        .badge-low { background: #28a745; color: white; }
        .badge-open { background: #17a2b8; color: white; }
        .badge-in_progress { background: #6610f2; color: white; }
        .badge-resolved { background: #28a745; color: white; }
        .badge-closed { background: #6c757d; color: white; }
        .escalated { color: #dc3545; font-weight: bold; }
        .ai-badge { background: #6610f2; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px; }
        .summary-box { background: #f8f9fa; border-left: 4px solid #007bff; padding: 15px; margin: 10px 0; }
        .action-box { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 10px 0; }
        .meta { color: #666; font-size: 14px; margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee; }
        .grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .description { white-space: pre-wrap; line-height: 1.6; }
        .success-msg { background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; padding: 10px; margin-bottom: 15px; color: #155724; }
    </style>
</head>
<body>
    <div class="container">
        @if(session('success'))
            <div class="success-msg">{{ session('success') }}</div>
        @endif

        <div class="header">
            <h1>Issue #{{ $issue->id }}</h1>
            <div>
                <a href="/issues" class="btn btn-secondary">Back</a>
                @if($issue->status->value !== 'resolved' && $issue->status->value !== 'closed')
                    <a href="/issues/{{ $issue->id }}/edit" class="btn">Edit</a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="grid">
                <div class="field">
                    <div class="field-label">Title</div>
                    <div class="field-value">{{ $issue->title }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Priority</div>
                    <div class="field-value">
                        <span class="badge badge-{{ $issue->priority->value }}">{{ ucfirst($issue->priority->label()) }}</span>
                        @if($issue->escalated_at) <span class="escalated">ESCALATED</span> @endif
                    </div>
                </div>
                <div class="field">
                    <div class="field-label">Category</div>
                    <div class="field-value">{{ $issue->category->label() }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Status</div>
                    <div class="field-value">
                        <span class="badge badge-{{ $issue->status->value }}">{{ $issue->status->label() }}</span>
                    </div>
                </div>
            </div>

            <div class="field" style="margin-top: 20px;">
                <div class="field-label">Description</div>
                <div class="description">{{ $issue->description }}</div>
            </div>

            @if($issue->summary)
                <div class="field">
                    <div class="field-label">Summary</div>
                    <div class="summary-box">{{ $issue->summary }}</div>
                </div>
            @endif

            @if($issue->suggested_action)
                <div class="field">
                    <div class="field-label">Suggested Next Action</div>
                    <div class="action-box">{{ $issue->suggested_action }}</div>
                </div>
            @endif

            @if($issue->escalated_at)
                <div class="field">
                    <div class="field-label">Escalated At</div>
                    <div class="field-value escalated">{{ $issue->escalated_at->format('M d, Y H:i:s') }}</div>
                </div>
            @endif

            <div class="meta">
                <div>Created: {{ $issue->created_at->format('M d, Y H:i:s') }}</div>
                <div>Updated: {{ $issue->updated_at->format('M d, Y H:i:s') }}</div>
            </div>
        </div>

        @if($issue->status->value !== 'resolved' && $issue->status->value !== 'closed')
            <div class="card">
                <h3 style="margin-bottom: 15px;">Quick Actions</h3>
                <form method="POST" action="/issues/{{ $issue->id }}/status" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="in_progress">
                    <button type="submit" class="btn">Mark In Progress</button>
                </form>
                <form method="POST" action="/issues/{{ $issue->id }}/status" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="resolved">
                    <button type="submit" class="btn btn-success">Mark Resolved</button>
                </form>
                <form method="POST" action="/issues/{{ $issue->id }}" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Issue</button>
                </form>
            </div>
        @endif
    </div>
</body>
</html>