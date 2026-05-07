<?php

namespace App\Http\Controllers;

use App\Enums\IssueCategory;
use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Services\IssueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IssueController extends Controller
{
    public function __construct(
        private IssueService $issueService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $status = null;
        if ($request->filled('status') && $request->input('status') !== '') {
            $status = IssueStatus::from($request->input('status'));
        }

        $category = null;
        if ($request->filled('category') && $request->input('category') !== '') {
            $category = IssueCategory::from($request->input('category'));
        }

        $priority = null;
        if ($request->filled('priority') && $request->input('priority') !== '') {
            $priority = IssuePriority::from($request->input('priority'));
        }

        $issues = $this->issueService->listIssues($status, $category, $priority);

        return response()->json($issues);
    }

    public function store(StoreIssueRequest $request): JsonResponse
    {
        $issue = $this->issueService->createIssue($request->validated());

        return response()->json([
            'message' => 'Issue created successfully',
            'data' => $issue,
            'ai_generated' => $issue->ai_generated,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $issue = $this->issueService->getIssue($id);
        return response()->json(['data' => $issue]);
    }

    public function update(UpdateIssueRequest $request, int $id): JsonResponse
    {
        $issue = $this->issueService->updateIssue($id, $request->validated());

        return response()->json([
            'message' => 'Issue updated successfully',
            'data' => $issue,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->issueService->deleteIssue($id);
        return response()->json(['message' => 'Issue deleted successfully'], 200);
    }

    // Web UI Methods
    public function webIndex(Request $request): View
    {
        $status = null;
        if ($request->filled('status') && $request->input('status') !== '') {
            $status = IssueStatus::from($request->input('status'));
        }

        $category = null;
        if ($request->filled('category') && $request->input('category') !== '') {
            $category = IssueCategory::from($request->input('category'));
        }

        $priority = null;
        if ($request->filled('priority') && $request->input('priority') !== '') {
            $priority = IssuePriority::from($request->input('priority'));
        }

        $issues = $this->issueService->listIssues($status, $category, $priority);
        return view('issues.index', compact('issues'));
    }

    public function webCreate(): View
    {
        return view('issues.create');
    }

    public function webStore(StoreIssueRequest $request): RedirectResponse
    {
        $issue = $this->issueService->createIssue($request->validated());
        return redirect("/issues/{$issue->id}")->with('success', 'Issue created successfully!');
    }

    public function webShow(int $id): View
    {
        $issue = $this->issueService->getIssue($id);
        return view('issues.show', compact('issue'));
    }

    public function webEdit(int $id): View
    {
        $issue = $this->issueService->getIssue($id);
        return view('issues.create', compact('issue'));
    }

    public function webUpdate(UpdateIssueRequest $request, int $id): RedirectResponse
    {
        $issue = $this->issueService->updateIssue($id, $request->validated());
        return redirect("/issues/{$issue->id}")->with('success', 'Issue updated successfully!');
    }

    public function webUpdateStatus(Request $request, int $id): RedirectResponse
    {
        $status = IssueStatus::from($request->input('status'));
        $this->issueService->updateIssueStatus($id, $status);
        return redirect("/issues/{$id}")->with('success', 'Issue status updated!');
    }

    public function webDestroy(int $id): RedirectResponse
    {
        $this->issueService->deleteIssue($id);
        return redirect('/issues')->with('success', 'Issue deleted successfully!');
    }
}
