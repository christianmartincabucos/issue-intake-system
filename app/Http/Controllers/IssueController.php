<?php

namespace App\Http\Controllers;

use App\Enums\IssueStatus;
use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Issue;
use App\Services\IssueSummaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IssueController extends Controller
{
    public function __construct(
        private IssueSummaryService $summaryService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Issue::query();

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        $issues = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($issues);
    }

    public function store(StoreIssueRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (!isset($data['status'])) {
            $data['status'] = IssueStatus::OPEN;
        }

        $issue = Issue::create($data);

        $summaryData = $this->summaryService->generateForIssue($issue);
        $issue->update($summaryData);

        if ($issue->needsEscalation()) {
            $issue->update(['escalated_at' => now()]);
        }

        return response()->json([
            'message' => 'Issue created successfully',
            'data' => $issue,
            'ai_generated' => $summaryData['ai_generated'],
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $issue = Issue::findOrFail($id);

        return response()->json(['data' => $issue]);
    }

    public function update(UpdateIssueRequest $request, int $id): JsonResponse
    {
        $issue = Issue::findOrFail($id);
        $wasEscalated = $issue->isEscalated();

        $issue->update($request->validated());

        if ($issue->needsEscalation() && !$wasEscalated) {
            $issue->update(['escalated_at' => now()]);
        }

        if ($request->has('description') || $request->has('title')) {
            $summaryData = $this->summaryService->generateForIssue($issue);
            $issue->update($summaryData);
        }

        return response()->json([
            'message' => 'Issue updated successfully',
            'data' => $issue,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $issue = Issue::findOrFail($id);
        $issue->delete();

        return response()->json(['message' => 'Issue deleted successfully'], 200);
    }

    // Web UI Methods
    public function webIndex(Request $request): View
    {
        $query = Issue::query();

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->has('category')) {
            $query->where('category', $request->input('category'));
        }
        if ($request->has('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        $issues = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('issues.index', compact('issues'));
    }

    public function webCreate(): View
    {
        return view('issues.create');
    }

    public function webStore(StoreIssueRequest $request): RedirectResponse
    {
        $issue = Issue::create($request->validated());
        $summaryData = $this->summaryService->generateForIssue($issue);
        $issue->update($summaryData);

        if ($issue->needsEscalation()) {
            $issue->update(['escalated_at' => now()]);
        }

        return redirect("/issues/{$issue->id}")->with('success', 'Issue created successfully!');
    }

    public function webShow(int $id): View
    {
        $issue = Issue::findOrFail($id);
        return view('issues.show', compact('issue'));
    }

    public function webEdit(int $id): View
    {
        $issue = Issue::findOrFail($id);
        return view('issues.create', compact('issue'));
    }

    public function webUpdate(UpdateIssueRequest $request, int $id): RedirectResponse
    {
        $issue = Issue::findOrFail($id);
        $issue->update($request->validated());

        if ($issue->needsEscalation() && !$issue->isEscalated()) {
            $issue->update(['escalated_at' => now()]);
        }

        return redirect("/issues/{$issue->id}")->with('success', 'Issue updated successfully!');
    }

    public function webUpdateStatus(Request $request, int $id): RedirectResponse
    {
        $issue = Issue::findOrFail($id);
        $issue->update(['status' => $request->input('status')]);

        return redirect("/issues/{$issue->id}")->with('success', 'Issue status updated!');
    }

    public function webDestroy(int $id): RedirectResponse
    {
        $issue = Issue::findOrFail($id);
        $issue->delete();

        return redirect('/issues')->with('success', 'Issue deleted successfully!');
    }
}
