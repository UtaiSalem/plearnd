<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\CourseMember;
use Illuminate\Http\Request;
use App\Models\CourseGroupMember;
use App\Http\Resources\CourseResource;
use App\Http\Resources\LessonResource;
use App\Http\Resources\CourseGroupResource;
use App\Http\Resources\CourseMemberResource;

class CourseMemberController extends Controller
{
    public function show(Course $course, CourseGroup $group, CourseMember $member)
    {
        return Inertia::render('Learn/Course/Member/Member', [
            'isCourseAdmin' => $course->user_id === auth()->id(),
            'course'        => new CourseResource($course),
            'lessons'       => LessonResource::collection($course->lessons),
            'groups'        => CourseGroupResource::collection($course->courseGroups),
            'member'        => new CourseMemberResource($member)
        ]);
    }

    public function storemember(Course $course, Request $request)
    {
        if (auth()->user()->pp < $course->tuition_fees) {
            return response()->json([
                'success' => false,
                'msg'     => 'แต้มสะสมไม่เพียงพอ กรุณาเติมแต้มสะสมก่อนสมัครสมาชิก'
            ], 201);
        }

        $course_member = CourseMember::where('course_id', $course->id)->where('user_id', auth()->id())->first();

        if (!$course_member) {
            $new_course_member = new CourseMember();
            $new_course_member->user_id     = auth()->id();
            $new_course_member->course_id   = $course->id;
            $new_course_member->group_id    = $request->group_id ?? null;
            $new_course_member->status      = $course->courseSettings->auto_accept_members === 0 ? 0 : 1;
            $new_course_member->save();

            $newCourseGroupMember = new CourseGroupMember();
            $newCourseGroupMember->user_id      = auth()->id();
            $newCourseGroupMember->course_id    = $new_course_member->course_id;
            $newCourseGroupMember->group_id     = $new_course_member->group_id;
            $newCourseGroupMember->status       = $course->courseSettings->auto_accept_members === 0 ? 0 : 1;
            $newCourseGroupMember->save();

            if ($new_course_member->status == 1) { $course->increment('enrolled_students'); }
            
            // if ($course->courseSettings->auto_accept_members == 1) {
            //     $new_course_member = $course->courseMembers()->create([
            //         'user_id'   => auth()->id(),
            //         'group_id'  => $request->group_id ?? null,
            //         'status'    => '1',
            //     ]);
            //     $course->increment('enrolled_students');
            // }else {
            //     $new_course_member = $course->courseMembers()->create([
            //         'user_id'   => auth()->id(),
            //         'group_id'  => $request->group_id ?? null,
            //         'status'    => '0',
            //     ]);
            // }
            
            // if ($new_course_member->group_id) {
            //     $newCourseGroupMember = new CourseGroupMember();
            //     $newCourseGroupMember->course_id = $new_course_member->course_id;
            //     $newCourseGroupMember->group_id = $new_course_member->group_id;
            //     $newCourseGroupMember->user_id = auth()->id();
            //     $newCourseGroupMember->status = $new_course_member->status;
            //     $newCourseGroupMember->save();
            // }

        }else{

            $course_member->group_id = $request->group_id;
            $course_member->save();
            $new_course_member = $course_member;

            $courseGroupMember = CourseGroupMember::where('course_id', $course->id)->where('user_id', auth()->id())->first();
            $courseGroupMember->group_id = $request->group_id;
            $courseGroupMember->save();
        }

        return response()->json([
            'success' => true,
            'newCourseMember' => CourseMember::find($new_course_member->id),
            // 'newCourseMember' => new CourseMemberResource(CourseMember::find($new_course_member->id)),
            // 'setting' =>  $curent_member_status,
        ], 200);
    }

    public function destroy(Course $course, CourseMember $member)
    {     
        try {

            $member_group = CourseGroupMember::where('group_id', $member->group_id)->where('user_id', auth()->id())->first();
            $member_group->delete();
            
            $member->delete();
            
            if ($member->status == 1) {
                if($course->enrolled_students > 0){ $course->decrement('enrolled_students'); };
            }

            return response()->json([
                'success' => true,
                // 'course_member' => $member,
                // 'course_group_member' => $member_group,
                // 'memberStatus' => $course->member_status($course->id),
                // 'enrolledStudents' => $course->enrolled_students,
            ], 200);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function setActiveTab(Course $course, CourseMember $member, Request $request)
    {
        $member->update([
            'last_accessed_tab' => $request->tab
        ]);
    }

    //function update
    public function update(Course $course, CourseMember $member, Request $request)
    {
        $member->update([
            'member_name'   => $request->member_name,
            'order_number'  => $request->order_number,
        ]);

        return response()->json([
            'success' => true,
            'request' => $request->all(),
        ], 200);
    }

}
