<?php

namespace App\Http\Controllers\Learn\Course\groups;

use App\Http\Controllers\Controller;
use App\Models\Learn\Course\info\Course;
use App\Models\Learn\Course\groups\CourseGroup;
use Illuminate\Http\Request;
use App\Http\Resources\Learn\Course\groups\CourseGroupResourceV2;
use App\Http\Resources\Learn\Course\members\CourseMemberResourceV2;

class CourseGroupController extends Controller
{
    /**
     * API endpoint for listing groups with pagination and filtering
     */
    public function index(Course $course, Request $request)
    {
        $query = $course->courseGroups()->withCount('members');

        // Apply filters
        if ($request->has('status') && $request->status !== null) {
            $query->where('status', $request->status);
        }

        // Search by name or description
        if ($request->has('search') && $request->search) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm);
            });
        }

        $groups = $query->orderBy('name')->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => CourseGroupResourceV2::collection($groups),
            'pagination' => [
                'current_page' => $groups->currentPage(),
                'last_page' => $groups->lastPage(),
                'per_page' => $groups->perPage(),
                'total' => $groups->total(),
            ],
        ]);
    }

    /**
     * API endpoint for storing a new group
     */
    public function store(Course $course, Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'status' => 'nullable|boolean',
            ]);

            $newGroup = $course->courseGroups()->create([
                'user_id' => auth()->id(),
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'] ?? 1,
            ]);

            return response()->json([
                'success' => true,
                'data' => new CourseGroupResourceV2($newGroup),
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * API endpoint for updating a group
     */
    public function update(Request $request, $groupId)
    {
        try {
            $group = CourseGroup::findOrFail($groupId);
            
            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:500',
                'status' => 'nullable|boolean',
            ]);

            $group->update($validated);

            return response()->json([
                'success' => true,
                'data' => new CourseGroupResourceV2($group->fresh()),
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * API endpoint for getting group members
     */
    public function members($groupId, Request $request)
    {
        try {
            $group = CourseGroup::findOrFail($groupId);
            
            $query = $group->members()->with('user');

            // Apply filters
            if ($request->has('status') && $request->status !== null) {
                $query->where('status', $request->status);
            }

            // Search by name or email
            if ($request->has('search') && $request->search) {
                $searchTerm = '%' . $request->search . '%';
                $query->whereHas('user', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                      ->orWhere('email', 'like', $searchTerm);
                });
            }

            $members = $query->orderBy('order_number')->paginate($request->get('per_page', 20));

            return response()->json([
                'success' => true,
                'data' => CourseMemberResourceV2::collection($members),
                'pagination' => [
                    'current_page' => $members->currentPage(),
                    'last_page' => $members->lastPage(),
                    'per_page' => $members->perPage(),
                    'total' => $members->total(),
                ],
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
