<?php

namespace App\Http\Controllers\Api;

use App\Models\FinalProject;
use App\Models\Keyword;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    /**
     * Get all projects
     */
    public function index(Request $request)
    {
        try {
            $query = FinalProject::with(['user', 'category', 'keywords']);

            if ($request->user()->role === 'mahasiswa') {
                $query->where('user_id', $request->user()->id);
            }

            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%$search%")
                        ->orWhere('abstract', 'like', "%$search%");
                });
            }

            $projects = $query->paginate($request->per_page ?? 15);

            return response()->json([
                'success' => true,
                'message' => 'Projects retrieved successfully',
                'data' => $projects,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve projects: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create new project
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'abstract' => 'required|string|min:100',
            'research_method' => 'sometimes|string|min:50',
            'keywords' => 'sometimes|array|min:1',
            'keywords.*' => 'string|max:50',
            'proposal_file' => 'sometimes|file|mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $projectData = $request->only(['category_id', 'title', 'abstract', 'research_method']);
            $projectData['user_id'] = $request->user()->id;

            if ($request->hasFile('proposal_file')) {
                $file = $request->file('proposal_file');
                $filename = 'proposal_' . time() . '.pdf';
                $file->storeAs('proposals', $filename, 'public');
                $projectData['proposal_file'] = 'proposals/' . $filename;
            }

            $project = FinalProject::create($projectData);

            // Add keywords
            if ($request->has('keywords')) {
                foreach ($request->keywords as $keyword) {
                    Keyword::create([
                        'final_project_id' => $project->id,
                        'keyword' => strtolower($keyword),
                    ]);
                }
            }

            $project->load(['user', 'category', 'keywords']);

            return response()->json([
                'success' => true,
                'message' => 'Project created successfully',
                'data' => $project,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Project creation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get project detail
     */
    public function show(Request $request, $projectId)
    {
        try {
            $project = FinalProject::with(['user', 'category', 'keywords', 'recommendations'])
                ->find($projectId);

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project not found',
                ], 404);
            }

            // Check authorization
            if ($request->user()->role === 'mahasiswa' && $project->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Project retrieved successfully',
                'data' => $project,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve project: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update project
     */
    public function update(Request $request, $projectId)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'sometimes|exists:categories,id',
            'title' => 'sometimes|string|max:255',
            'abstract' => 'sometimes|string|min:100',
            'research_method' => 'sometimes|string|min:50',
            'keywords' => 'sometimes|array|min:1',
            'keywords.*' => 'string|max:50',
            'proposal_file' => 'sometimes|file|mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $project = FinalProject::find($projectId);

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project not found',
                ], 404);
            }

            // Check authorization
            if ($request->user()->role === 'mahasiswa' && $project->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            $project->update($request->only(['category_id', 'title', 'abstract', 'research_method']));

            if ($request->hasFile('proposal_file')) {
                $file = $request->file('proposal_file');
                $filename = 'proposal_' . time() . '.pdf';
                $file->storeAs('proposals', $filename, 'public');
                $project->proposal_file = 'proposals/' . $filename;
                $project->save();
            }

            // Update keywords if provided
            if ($request->has('keywords')) {
                $project->keywords()->delete();
                foreach ($request->keywords as $keyword) {
                    Keyword::create([
                        'final_project_id' => $project->id,
                        'keyword' => strtolower($keyword),
                    ]);
                }
            }

            $project->load(['user', 'category', 'keywords']);

            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully',
                'data' => $project,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Project update failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete project
     */
    public function destroy(Request $request, $projectId)
    {
        try {
            $project = FinalProject::find($projectId);

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project not found',
                ], 404);
            }

            // Check authorization
            if ($request->user()->role === 'mahasiswa' && $project->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            $project->delete();

            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Project deletion failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
