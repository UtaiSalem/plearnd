<?php

namespace App\Http\Controllers\Learn;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Learn\Course;
use App\Models\Learn\Academy;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\Learn\CourseMember;
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
        // $courses = $activities = $this->getMoreCourses();
        $courses =  $this->getMoreCourses();
        
        return Inertia::render('Learn/Course/Courses', [
            'courses'       => $courses->original['courses'],
        ]);
    }

    public function getMoreCourses()
    {
        return response()->json([
            'success'       => true,
            'courses'       => CourseResource::collection(Course::latest()->paginate()),
        ], 200);
    }

    public function getUserCourses(User $user)
    {
        return Inertia::render('Learn/Course/UserCourses', [
            'courses'           => CourseResource::collection($user->courses()->latest()->paginate()),
        ]);
    }

    public function getMyCourses(User $user)
    {
        return response()->json([
            'success'   => true,
            'courses'   => CourseResource::collection($user->courses()->latest()->paginate()),
        ], 200);
    }


    public function getAuthMemberCourses(User $user)
    {
        $authMemberCourse = CourseMember::where('user_id', auth()->id())->pluck('course_id')->all();
        $coursesAuthMember = CourseResource::collection(Course::whereIn('id', $authMemberCourse)->latest()->paginate());

        return Inertia::render('Learn/Course/AuthMemberCourses', [
            'courses'           => $coursesAuthMember,
        ]);
    }

    public function getAuthMemberedCourses(User $user)
    {
        $authMemberCourse = CourseMember::where('user_id', auth()->id())->pluck('course_id')->all();
        $coursesAuthMember = CourseResource::collection(Course::whereIn('id', $authMemberCourse)->latest()->paginate());

        return response()->json([
            'success'           => true,
            'courses'           => $coursesAuthMember,
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
            // $validated = $request->validate([
            //     'name'              => 'required|string|max:255',
            //     'cover'                => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:4096',
            // ]);

            //validate for all of this request academy_id,code,name,description,category,level,credit_units,hours_per_week,start_date,end_date,auto_accept_members,saleable,price,status,cover
            $validated = $request->validate([
                'academy_id'        => 'nullable',
                'code'              => 'nullable',
                'name'              => 'required|string|max:255',
                'description'       => 'nullable|string',
                'category'          => 'nullable|string',
                'level'             => 'nullable|string',
                'credit_units'      => 'nullable|numeric',
                'hours_per_week'    => 'nullable|numeric',
                'start_date'        => 'nullable|date',
                'end_date'          => 'nullable|date',
                'auto_accept_members'=> 'nullable|boolean',
                'saleable'          => 'nullable|boolean',
                'price'             => 'nullable|numeric',
                'status'            => 'nullable|string',
                'cover'             => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:4096',
            ]);

            if($request->file('cover')) {
                $cover_file = $request->file('cover');
                $cover_filename =  uniqid().'.'.$cover_file->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('images/courses/covers', $cover_file, $cover_filename);              
                $validated['cover'] = $cover_filename;
            }


            // return response()->json([
            //     'success'   => true,
            //     'newCourse' => $request->all(),
            // ], 200);
            
            $newCourse = new Course();
            $newCourse->academy_id       = $request->academy_id;
            $newCourse->user_id          = auth()->id();
            $newCourse->instructor_id    = auth()->id();
            $newCourse->code             = $request->code;
            $newCourse->name             = $request->name;
            $newCourse->slug             = Str::slug($request->name);
            $newCourse->description      = $request->description;
            $newCourse->category         = $request->category;
            $newCourse->level            = $request->level;
            $newCourse->credit_units     = $request->credit_units;
            $newCourse->hours_per_week   = $request->hours_per_week;
            $newCourse->start_date       = Carbon::parse($validated['start_date'])->setTimezone('Asia/Bangkok');
            $newCourse->end_date         = Carbon::parse($validated['end_date'])->setTimezone('Asia/Bangkok');
            // $newCourse->auto_accept_members = $request->auto_accept_members;
            $newCourse->saleable         = $request->saleable;
            $newCourse->price            = $request->price;
            $newCourse->status           = $request->status;
            $newCourse->cover            = $validated['cover'] ?? '';
                        
            $newCourse->save();

            if ($newCourse) {
                $newCourse->courseSettings()->create([
                    'auto_accept_members' => $request->auto_accept_members ? 1 : 0,
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
        return to_route('course.feeds', $course->id);
    }

    public function edit(Course $course)
    {

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
            'name'              => 'nullable|string',
            'slug'              => 'nullable',
            'code'              => 'nullable',
            'description'       => 'nullable',
            'duration'          => 'nullable',
            'tuition_fees'      => 'nullable|numeric',
            'price'             => 'nullable|numeric',
            'credit_units'      => 'nullable|numeric',
            'hours_per_week'    => 'nullable|numeric',
            'category'          => 'nullable',
            'capacity'          => 'nullable|numeric',
            'level'             => 'nullable',

        ]);

        $validated['name']          = $request->name ?? $course->name;
        $validated['start_date']    = $request->start_date == 'null' || $request->start_date == 'undefined' ? null : Carbon::parse($request->start_date);
        $validated['end_date']      = $request->end_date == 'null' || $request->end_date == 'undefined' ? null : Carbon::parse($request->end_date);

        $validated['status']        = $request->status ?? $course->status;
        $validated['saleable']      = $request->saleable;
        
        $course->update($validated);

        $course->courseSettings()->update([
            'auto_accept_members' => $request->auto_accept_members ?? $course->courseSettings->auto_accept_members,
        ]);

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

        $course->refresh();

        return response()->json([
            'success' => true,
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
            'groups'        => CourseGroupResource::collection($course->courseGroups),
            'assignments'       => AssignmentResource::collection($course->courseAssignments),      
            'quizzes'           => CourseQuizResource::collection($course->courseQuizzes),
            'members'           => CourseMemberResource::collection($course->courseMembers),
            'courseMembersProgress' => $courseMembersProgress,
            'courseMemberOfAuth'=> $course->courseMembers()->where('user_id', auth()->id())->first(),
        ]);

    }

    public function settings(Course $course)
    {
        return Inertia::render('Learn/Course/Setting/Settings', [
            'course'                => new CourseResource($course),
            'isCourseAdmin'         => $course->user_id === auth()->id(),
            'courseMemberOfAuth'   => $course->courseMembers()->where('user_id', auth()->id())->first(),
        ]);
    }

    public function basicInfo(Course $course){
        return Inertia::render('Learn/Course/Basic/BasicInfo', [
            'course'                => new CourseResource($course),
            'isCourseAdmin'         => $course->user_id === auth()->id(),
            'courseMemberOfAuth'    => $course->courseMembers()->where('user_id', auth()->id())->first(),
        ]);
    }

}







