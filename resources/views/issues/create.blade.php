<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($issue) ? 'Edit Issue' : 'Create Issue' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        h1 { color: #333; }
        .btn { padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; display: inline-block; }
        .btn:hover { background: #0056b3; }
        .btn-secondary { background: #6c757d; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #333; font-weight: 500; }
        input, textarea, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        textarea { min-height: 120px; resize: vertical; }
        .error { color: #dc3545; font-size: 14px; margin-top: 5px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .info { background: #e7f3ff; border: 1px solid #b8daff; border-radius: 4px; padding: 10px; margin-bottom: 15px; font-size: 14px; color: #004085; }
        .alert { background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; padding: 10px; margin-bottom: 15px; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ isset($issue) ? 'Edit Issue #' . $issue->id : 'Create New Issue' }}</h1>
            <a href="/issues" class="btn btn-secondary">Back</a>
        </div>

        <div class="card">
            @if(!isset($issue))
                <div class="info">
                    <strong>Note:</strong> AI-generated summary and suggested action will be automatically created for your issue.
                </div>
            @endif

            @if($errors->any())
                <div class="alert">
                    <strong>Please fix the following errors:</strong>
                    <ul style="margin: 10px 0 0 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ isset($issue) ? '/issues/' . $issue->id : '/issues' }}">
                @csrf
                @if(isset($issue))
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $issue->title ?? '') }}" required maxlength="255" placeholder="Brief description of the issue">
                    @error('title') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" required maxlength="10000" placeholder="Detailed description of the issue">{{ old('description', $issue->description ?? '') }}</textarea>
                    @error('description') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="priority">Priority</label>
                    <select id="priority" name="priority">
                        <option value="low" {{ old('priority', $issue->priority->value ?? 'medium') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', $issue->priority->value ?? 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority', $issue->priority->value ?? 'medium') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ old('priority', $issue->priority->value ?? 'medium') == 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                    @error('priority') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category">
                        <option value="support" {{ old('category', $issue->category->value ?? 'support') == 'support' ? 'selected' : '' }}>General Support</option>
                        <option value="bug" {{ old('category', $issue->category->value ?? 'support') == 'bug' ? 'selected' : '' }}>Bug Report</option>
                        <option value="feature_request" {{ old('category', $issue->category->value ?? 'support') == 'feature_request' ? 'selected' : '' }}>Feature Request</option>
                        <option value="infrastructure" {{ old('category', $issue->category->value ?? 'support') == 'infrastructure' ? 'selected' : '' }}>Infrastructure</option>
                        <option value="security" {{ old('category', $issue->category->value ?? 'support') == 'security' ? 'selected' : '' }}>Security</option>
                        <option value="other" {{ old('category', $issue->category->value ?? 'support') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('category') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn">{{ isset($issue) ? 'Update Issue' : 'Create Issue' }}</button>
                    <a href="/issues" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>