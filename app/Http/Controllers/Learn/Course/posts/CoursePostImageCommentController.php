<?php

namespace App\Http\Controllers\Learn\Course\Posts;

use App\Models\Shared\User;
use App\Models\Learn\Course\info\Course;
use App\Models\Learn\Course\posts\CoursePost;
use Illuminate\Http\Request;
use App\Models\Learn\Course\posts\CoursePostImage;
use App\Http\Resources\Learn\posts\CoursePostImageCommentResource;

class CoursePostImageCommentController extends Controller
{
    public $plearndAdmin = null;

    public function __construct()
    {
        $this->plearndAdmin = User::find(1);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Course $course, CoursePost $post, CoursePostImage $image)
    {
        $comments = $image->image_comments()->latest()->paginate();

        return response()->json([
            'success' => true,
            'comments' => CoursePostImageCommentResource::collection($comments),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Course $course, CoursePost $post, CoursePostImage $image, Request $request,)
    {
        try {
            $validatedData = $request->validate([
                'content' => 'nullable|string',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $newComment = $image->image_comments()->create([
                'user_id' => auth()->id(),
                'content' => $validatedData['content']
            ]);

            $image->increment('comments', 1);

            return response()->json([
                'success' => true,
                'comment' => new CoursePostImageCommentResource($newComment),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 404);
        }
    }
}
