<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Tracker</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        h1 { color: #333; }
        .btn { padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .btn-secondary { background: #6c757d; }
        .filters { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .filters select, .filters input { padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        table { width: 100%; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; font-weight: 600; }
        tr:hover { background: #f8f9fa; }
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
        .empty { text-align: center; padding: 40px; color: #666; }
        .pagination { margin-top: 20px; display: flex; justify-content: center; }
    </style>
</head>
<body>
    @if(session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; padding: 10px; margin-bottom: 15px; color: #155724;">
            {{ session('success') }}
        </div>
    @endif
    <div class="container">
        <div class="header">
            <h1>Issue Tracker</h1>
            <a href="/issues/create" class="btn">+ New Issue</a>
        </div>

        <form method="GET" action="/issues" class="filters">
            <select name="status">
                <option value="">All Statuses</option>
                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
            <select name="priority">
                <option value="">All Priorities</option>
                <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
            </select>
            <select name="category">
                <option value="">All Categories</option>
                <option value="bug" {{ request('category') == 'bug' ? 'selected' : '' }}>Bug</option>
                <option value="feature_request" {{ request('category') == 'feature_request' ? 'selected' : '' }}>Feature Request</option>
                <option value="support" {{ request('category') == 'support' ? 'selected' : '' }}>Support</option>
                <option value="infrastructure" {{ request('category') == 'infrastructure' ? 'selected' : '' }}>Infrastructure</option>
                <option value="security" {{ request('category') == 'security' ? 'selected' : '' }}>Security</option>
                <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
            <button type="submit" class="btn">Filter</button>
            <a href="/issues" class="btn btn-secondary">Clear</a>
        </form>

        @if($issues->isEmpty())
            <div class="empty">No issues found. <a href="/issues/create">Create one</a>.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Priority</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Escalated</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($issues as $issue)
                        <tr>
                            <td>{{ $issue->id }}</td>
                            <td><a href="/issues/{{ $issue->id }}" style="color: #007bff; text-decoration: none;">{{ $issue->title }}</a></td>
                            <td><span class="badge badge-{{ $issue->priority->value }}">{{ ucfirst($issue->priority->label()) }}</span></td>
                            <td>{{ $issue->category->label() }}</td>
                            <td><span class="badge badge-{{ $issue->status->value }}">{{ $issue->status->label() }}</span></td>
                            <td class="{{ $issue->escalated_at ? 'escalated' : '' }}">{{ $issue->escalated_at ? 'Yes' : 'No' }}</td>
                            <td>{{ $issue->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination">
                {{ $issues->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</body>
</html>