<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    /** Display a listing of the resource. */
    public function index(Request $request): JsonResponse
    {
        $query = Project::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        // Search by name
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $projects = $query->latest('created_at')->get();

        return response()->json([
            'data' => $projects->values()->all(),
            'total' => $projects->count(),
        ]);
    }

    /** Store a newly created resource in storage. */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'client_id' => ['required', 'exists:clients,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:planning,in_progress,on_hold,completed,cancelled'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Validate date range: due_date must not precede start_date when both are provided
        if ($validated['start_date'] && $validated['due_date'] && $validated['due_date'] < $validated['start_date']) {
            return response()->json([
                'errors' => ['due_date' => ['Due date must not be before start date.']],
            ], 422);
        }

        $project = Project::create($validated);

        return response()->json([
            'data' => $project,
            'message' => 'Project created successfully.',
        ], 201);
    }

    /** Display the specified resource. */
    public function show(Project $project): JsonResponse
    {
        return response()->json([
            'data' => $project,
        ]);
    }

    /** Update the specified resource in storage. */
    public function update(Request $request, Project $project): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'client_id' => ['sometimes', 'required', 'exists:clients,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'required', 'in:planning,in_progress,on_hold,completed,cancelled'],
            'budget' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'start_date' => ['sometimes', 'nullable', 'date'],
            'due_date' => ['sometimes', 'nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Validate date range: due_date must not precede start_date when both are provided
        if (isset($validated['start_date']) && isset($validated['due_date']) && $validated['due_date'] < $validated['start_date']) {
            return response()->json([
                'errors' => ['due_date' => ['Due date must not be before start date.']],
            ], 422);
        }

        $project->update($validated);

        return response()->json([
            'data' => $project,
            'message' => 'Project updated successfully.',
        ]);
    }

    /** Remove the specified resource from storage. */
    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully.',
        ]);
    }
}
