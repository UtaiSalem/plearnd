<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Course;
use App\Models\Academy;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\CourseMember;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use App\Http\Resources\CourseResource;
use App\Http\Resources\LessonResource;
use App\Http\Resources\AcademyResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\QuestionResource;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\AssignmentResource;
use App\Http\Resources\CourseQuizResource;
use App\Http\Resources\CourseGroupResource;
use App\Http\Resources\CourseMemberResource;
use App\Http\Resources\CourseMemberGradeProgressResource;

class CourseController extends Controller
{
    public function index()
    {
        return Inertia::render('Learn/Course/Courses', [
            'courses'       => CourseResource::collection(Course::latest()->paginate(10)),
        ]);
    }

    public function getMoreCourses()
    {
        $courses = Course::latest()->paginate(10);
        return response()->json([
            'success'       => true,
            'courses'       => CourseResource::collection($courses),
        ], 200);
    }

    public function getUserCourses(User $user)
    {
        return Inertia::render('Learn/Course/UserCourses', [
            'headerTitle'       => 'รายวิชาของฉัน',
        ]);
    }

    public function getMyCourses(User $user)
    {
        return response()->json([
            'success'   => true,
            'courses'   => CourseResource::collection($user->courses),
        ], 200);
    }


    public function getAuthMemberCourses(User $user)
    {
        return Inertia::render('Learn/Course/AuthMemberCourses', [
            'headerTitle'       => 'รายวิชาที่สมัครเรียน',

        ]);
    }

    public function getAuthMemberedCourses(User $user)
    {
        $authMemberCourse = CourseMember::where('user_id', auth()->id())->pluck('course_id')->all();
        $coursesAuthMember = CourseResource::collection(Course::whereIn('id', $authMemberCourse)->get());

        return response()->json([
            'success'           => true,
            'courses'           => $coursesAuthMember,
            'headerTitle'       => 'รายวิชาที่สมัครเรียน'
        ], 200);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Learn/Course/CreateNewCourse');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'              => 'required|string|max:255',
                'cover'                => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:4096',
            ]);

            if($request->file('cover')) {
                $cover_file = $request->file('cover');
                $cover_filename =  uniqid().'.'.$cover_file->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('images/courses/covers', $cover_file, $cover_filename);              
            }
            
            $newCourse = new Course();

            $newCourse->user_id         = auth()->id();
            $newCourse->instructor_id   = auth()->id();
            $newCourse->academy_id      = $request->academy_id == 'null' || $request->academy_id == null ? null : $academy->id ;
            $newCourse->name            = $validated['name'];
            $newCourse->code            = $request->code == 'null' || $request->code == null ? null : $request->code;
            $newCourse->description     = $request->description == 'null' || $request->description == null ? null: $request->description;
            $newCourse->duration        = $request->duration ?? null;
            $newCourse->start_date      = $request->start_date == 'null' || $request->start_date == 'undefined' ? null : Carbon::parse($request->start_date);
            $newCourse->end_date        = $request->end_date == 'null' || $request->end_date == 'undefined' ? null : Carbon::parse($request->end_date);
            $newCourse->credit_units    = $request->credit_units ?? null;
            $newCourse->hours_per_week  = $request->hours_per_week ?? null;
            $newCourse->category        = $request->category == 'null' || $request->category == null ? null : $request->category;
            $newCourse->tuition_fees    = $request->tuition_fees == 'null' || $request->tuition_fees == null ?  0 : $request->tuition_fees;
            $newCourse->status          = $request->status ==='true'? 1 : 0;
            $newCourse->saleable        = $request->saleable ==='true'? 1 : 0;
            $newCourse->price           = $request->price == 'null' || $request->price == null ? 0: $request->price;
            $newCourse->level           = $request->level == 'null' || $request->level == null ? null: $request->level;
            $newCourse->cover           = $cover_filename ?? null;
            
            $newCourse->save();

            if ($newCourse) {
                $newCourse->courseSettings()->create([
                    'auto_accept_members' => $request->auto_accept_members == 'true' || $request->auto_accept_members === true ? 1 : 0,
                ]);

                $newCourse->courseGroups()->create([
                    'user_id' => auth()->id(),
                    'name'      => 'กลุ่ม1',
                ]);

                $newCourse->courseMembers()->create([
                    'user_id' => auth()->id(),
                    'status' => 1,
                    'role' => 3,
                ]);
            }

            return response()->json([
                'success'   => true,
                'newCourse' => $newCourse,
            ], 200);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function show(Course $course)
    {
        $coursesResource = new CourseResource($course);
        $isCourseAdmin = $course->user_id == auth()->id() ? true: false;
        // $cma =  $course->courseMembers()->where('user_id', auth()->id())->first() ?? new CourseMember();

        return Inertia::render('Learn/Course/Course', [
            'success'           => true,
            'isCourseAdmin'     => $isCourseAdmin,
            'academy'           => $coursesResource->academy ? new AcademyResource($course->academy) : null,
            'course'            => $coursesResource,
            'lessons'           => LessonResource::collection($course->lessons()->latest()->paginate(10)),
            'assignments'       => AssignmentResource::collection($course->assignments),      
            'questions'         => QuestionResource::collection($course->questions),
            'quizzes'           => CourseQuizResource::collection($course->courseQuizzes),
            'members'           => CourseMemberResource::collection($course->courseMembers),
            'groups'            => CourseGroupResource::collection($course->courseGroups),
            'courseMemberOfAuth'=> $course->courseMembers()->where('user_id', auth()->id())->first(),
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
    public function update(Course $course, Request $request )
    {
        $validated = $request->validate([
            'user_id'           => 'nullable',
            'instructor_id'     => 'nullable',
            'academy_id'        => 'nullable',
            'name'              => 'nullable',
            'slug'              => 'nullable',
            'code'              => 'nullable',
            'description'       => 'nullable',
            'duration'          => 'nullable',
            'tuition_fees'      => 'nullable',
            'price'             => 'nullable',
            'credit_units'      => 'nullable',
            'hours_per_week'    => 'nullable',
            'category'          => 'nullable',
            'capacity'          => 'nullable',
            'level'             => 'nullable',

        ]);

        $validated['start_date'] = $request->start_date == 'null' || $request->start_date == 'undefined' ? null : Carbon::parse($request->start_date);
        $validated['end_date']   = $request->end_date == 'null' || $request->end_date == 'undefined' ? null : Carbon::parse($request->end_date);

        $validated['status']    = $request->status;
        $validated['saleable']  = $request->saleable ? '1':'0';
        
        $course->update($validated);

        if($request->hasFile('cover')) {

            $file = public_path().'\storage\images\courses\covers\\'. $course->cover;
            if ($course->cover && File::exists($file)) {
                File::delete($file); 
            }

            $cover_file = $request->file('cover');
            $cover_filename =  uniqid().'.'.$cover_file->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('images/courses/covers', $cover_file, $cover_filename); 
            
            $course->cover = $cover_filename;
            $course->save();
        }

        // $course->refresh();

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
        $lessons = $course->lessons;
        if ($lessons) {
            foreach ($lessons as $lesson) {
                if ($lesson->images) {
                    foreach ($lesson->images as $image) {
                        Storage::disk('public')->delete('images/courses/lessons/'. $image->image_url);
                        $image->delete();   
                    }
                }
                $lesson->delete();
            }
        }

        $course->courseSettings->delete();
        $course->delete();
        return response()->json([
            'success' => true,
        ], 200);
    }

    //function to process all member progrss and grade
    public function progress(Course $course)
    {
        $courseMembers = $course->courseMembers;
        $courseMembersProgress = [];
        foreach ($courseMembers as $member) {
            $memberProgress = $member->memberProgress;
            $courseMembersProgress[] = [
                'member' => $member,
                'progress' => $memberProgress,
            ];
        }

        return Inertia::render('Learn/Course/Progress/CourseMembersProgress', [
            'isCourseAdmin' => $course->user_id === auth()->id(),
            'course'        => new CourseResource($course),
            'lessons'       => LessonResource::collection($course->lessons),
            'groups'        => CourseGroupResource::collection($course->courseGroups),
            'assignments'       => AssignmentResource::collection($course->assignments),      
            'questions'         => QuestionResource::collection($course->questions),
            'quizzes'           => CourseQuizResource::collection($course->courseQuizzes),
            'members'           => CourseMemberResource::collection($course->courseMembers),
            'courseMembersProgress' => $courseMembersProgress,
        ]);

    }

}
