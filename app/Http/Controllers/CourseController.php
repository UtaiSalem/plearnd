<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Course;
use App\Models\Academy;
use Illuminate\Support\Str;
use App\Models\CourseMember;
use Illuminate\Http\Request;
use App\Http\Resources\CourseResource;
use App\Http\Resources\LessonResource;
use App\Http\Resources\AcademyResource;
use App\Http\Resources\QuestionResource;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\AssignmentResource;

class CourseController extends Controller
{
    public function index()
    {
        // $authMemberCourseIds = CourseMember::where('user_id', auth()->id())->get('user_id');
        // $authMemberCourses = CourseResource::collection(Course::where('user_id', $authMemberCourseIds));
        return Inertia::render('Courses', [
            // 'authMemberCourses' => $authMemberCourses,
            'allCourses' => CourseResource::collection(Course::all()),
            'authOwnerCourses' => CourseResource::collection(auth()->user()->courses),
            'authMemberCourses' => [],

            ]);
    }

    public function getUserCourses(User $user)
    {
        // $courses = Course::select('id', 'name')->where('user_id', $user->id)->get();
        $courses =Course::all();
        return response()->json([
            'user_courses' => $courses
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Courses/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $course = $user->courses()->create([
            'instructor_id' => $user->id,
            'code'          => $request['code'], 
            'name'          => $request['name'],
            'description'   => $request['description'],  
            'price'         => 0, 
            'slug'          => Str::slug($request['name']), 
        ]);
        
        return to_route('courses.show', $course->id);
    }

    public function show(Course $course)
    {
        $course->user_id == auth()->id() ? $isCourseAdmin = true: $isCourseAdmin = false;
        return Inertia::render('Course', [
            'isCourseAdmin' => $isCourseAdmin,
            // 'academy'       => new AcademyResource($course->academy),
            'course'        => new CourseResource($course),
            'lessons'       => LessonResource::collection($course->lessons),
            'assignments'   => AssignmentResource::collection($course->assignments),
            'questions'     => QuestionResource::collection($course->questions),
            'imagePath'     => '/../../'
        ]);
    }

    public function edit(Course $course)
    {
        // return Inertia::render('Course', [
        //     'course'=> $course,
        //     'lessons' => $course->lessons,
        //     'imagePath' => '/../../'
        // ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {

        $course->update($request->all());

        return response()->json([
            'success' => true,
            // 'course' => $course,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        //
    }
}
