<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\CourseMember;
use Illuminate\Http\Request;
use App\Models\CourseQuizResult;
use App\Models\CourseGroupMember;
use App\Models\UserAnswerQuestion;
use App\Http\Resources\CourseResource;
use App\Http\Resources\LessonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\AssignmentResource;
use App\Http\Resources\CourseQuizResource;
use App\Http\Resources\CourseGroupResource;
use App\Http\Resources\CourseMemberResource;
use App\Http\Resources\AssignmentAnswerResource;

class CourseMemberController extends Controller
{
    public function index(Course $course)
    {

        return Inertia::render('Learn/Course/Member/Members', [
            'course'                => new CourseResource($course),
            'members'               => CourseMemberResource::collection($course->courseMembers()->orderBy('order_number')->paginate()),
            'groups'                => CourseGroupResource::collection($course->courseGroups),
            'isCourseAdmin'         => $course->user_id === auth()->id(),
            'courseMemberOfAuth'    => $course->courseMembers()->where('user_id', auth()->id())->first(),
        ]);
    }

    public function show(Course $course, CourseGroup $group, CourseMember $member)
    {
        return Inertia::render('Learn/Course/Member/Member', [
            'isCourseAdmin' => $course->user_id === auth()->id(),
            'course'        => new CourseResource($course),
            'lessons'       => LessonResource::collection($course->courseLessons),
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

            $courseAutoAcceptMembers = $course->courseSettings->auto_accept_members === 0 ? 0 : 1;

            $new_course_member = new CourseMember();
            $new_course_member->user_id     = auth()->id();
            $new_course_member->course_id   = $course->id;
            $new_course_member->group_id    = $request->group_id ?? null;
            $new_course_member->status      = $courseAutoAcceptMembers;
            $new_course_member->course_member_status      = $courseAutoAcceptMembers;
            $new_course_member->save();

            $newCourseGroupMember = new CourseGroupMember();
            $newCourseGroupMember->user_id      = auth()->id();
            $newCourseGroupMember->course_id    = $new_course_member->course_id;
            $newCourseGroupMember->group_id     = $new_course_member->group_id;
            $newCourseGroupMember->status       = $courseAutoAcceptMembers;
            $newCourseGroupMember->save();

            if ($new_course_member->status == 1) { $course->increment('enrolled_students'); }
            
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

            $member_group = CourseGroupMember::where('group_id', $member->group_id)->where('user_id', $member->user_id)->first();
            $member_group->delete();
            
            
            if ($member->status == 1) {
                if($course->enrolled_students > 0){ $course->decrement('enrolled_students'); };
            }

            $member->delete();
            
            return response()->json([
                'success' => true,
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
    public function setActiveGroupTab(Course $course, CourseMember $member, Request $request)
    {
        $member->update([
            'last_accessed_group_tab' => $request->group_tab
        ]);

        return response()->json([
            'success' => true,
        ], 200);
    }

    //function update
    public function update(Course $course, CourseMember $member, Request $request)
    {
        $member->update([
            'member_name'   => $request->member_name,
            'order_number'  => $request->order_number,
            'member_code'   => $request->member_code,
        ]);

        if($member->user_id === auth()->id()){
            $member->update([
                'notes_comments' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'notes_comments' => $member->notes_comments
        ], 200);
    }

    // function delete course's member
    public function deleteCourseMember(Course $course, CourseMember $member)
    {
        
        try {
            $member_group = CourseGroupMember::where('course_id', $course->id)
                                                ->where('group_id', $member->group_id ?? null)
                                                ->where('user_id', $member->user_id)
                                                ->first();
            
            // if ($member_group) $member_group->delete();
            
            // if ($member->status == 1) {
            //     if($course->enrolled_students > 0) $course->decrement('enrolled_students');
            // }

            // $user_answer_questions = UserAnswerQuestion::where('course_id', $course->id)->where('user_id', $member->user_id)->get();
            // if ($user_answer_questions->count() > 0) {
            //     foreach ($user_answer_questions as $user_answer_question) {
            //         if($user_answer_question) $user_answer_question->delete();
            //     }
            // }

            // $course_quiz_results = CourseQuizResult::where('course_id', $course->id)->where('user_id', $member->user_id)->get();
            // if ($course_quiz_results->count() > 0) {
            //     foreach ($course_quiz_results as $course_quiz_result) {
            //         if($course_quiz_result) $course_quiz_result->delete();
            //     }
            // }

            $assignments = $course->assignments()->get();
            if ($assignments->count() > 0) {
                foreach ($assignments as $assignment) {
                    $user_assignment_answers =  AssignmentAnswerResource::collection($assignment->answers()->where('user_id', $member->user_id)->get());
                    foreach ($user_assignment_answers as $user_assignment_answer) {
                        if($user_assignment_answer->images->count() > 0) {
                            foreach ($user_assignment_answer->images as $image) {
                                Storage::disk('public')->delete('images/courses/assignments/answers/'. $image->filename);
                            }
                            $user_assignment_answer->images()->delete();
                        }
                        $user_assignment_answer->delete();
                    }            
                }
            }

            $user_answer_questions = UserAnswerQuestion::where('course_id', $course->id)->where('user_id', $member->user_id)->delete();
            $course_quiz_results = CourseQuizResult::where('course_id', $course->id)->where('user_id', $member->user_id)->delete();

            if ($member->status == 1) {
                if($course->enrolled_students > 0) $course->decrement('enrolled_students');
            }

            if ($member_group) $member_group->delete();

            $member->delete();
            
            return response()->json([
                'success' => true,
            ], 200);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //function update member bonus points
    public function updateBonusPoints(Course $course, CourseMember $member, Request $request)
    {
        $member->increment('bonus_points', $request->bonus_points);

        $member->update([
            'grade_progress' => $this->calculateGrade((($member->achieved_score+$request->bonus_points) / $course->total_score) * 100),
        ]);

        $member->refresh();
        
        return response()->json([
            'success' => true,          
        ], 200);
    }

    public function calculateGrade($score) {
        if ($score >= 80) {
            return '4';
        } else if ($score >= 75) {
            return '3.5';
        } else if ($score >= 70) {
            return '3';
        } else if ($score >= 65) {
            return '2.5';
        } else if ($score >= 60) {
            return '2';
        } else if ($score >= 55) {
            return '1.5';
        } else if ($score >= 50) {
            return '1';
        } else {
            return '0';
        }
    }

    public function updateGradeProgress(Course $course, CourseMember $member, Request $request)
    {
        $member->update([
            'grade_progress' => $request->grade_progress,
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    public function updateNotesComments(Course $course, CourseMember $member, Request $request)
    {
        $member->update([
            'notes_comments' => $request->notes_comments,
        ]);

        return response()->json([
            'success' => true,
            'notes_comments' => $member->notes_comments
        ]);
    }

    public function getMembersRequesters(Course $course)
    {
        $members = $course->courseMembers()->where('course_member_status', 0)->latest()->paginate();

        return Inertia::render('Learn/Course/CourseMemberRequesters', [
            'course' => new CourseResource($course),
            'groups' => CourseGroupResource::collection($course->courseGroups),
            'members' => CourseMemberResource::collection($members),
            'isCourseAdmin' => $course->user_id === auth()->id(),
            'courseMemberOfAuth' => $course->courseMembers()->where('user_id', auth()->id())->first(),
        ]);
    }

    public function memberSettings(Course $course, CourseMember $course_member)
    {
        return Inertia::render('Learn/Course/Member/MemberSettings', [
            'isCourseAdmin' => $course->user_id === auth()->id(),
            'course'        => new CourseResource($course),
            'lessons'       => LessonResource::collection($course->courseLessons),
            'groups'        => CourseGroupResource::collection($course->courseGroups),
            'member'        => new CourseMemberResource($course_member),
            'assignments'       => AssignmentResource::collection($course->courseAssignments), 
            'quizzes'           => CourseQuizResource::collection($course->courseQuizzes),
            'courseMemberOfAuth' => $course->courseMembers()->where('user_id', auth()->id())->first(),
            // 'courseMemberOfAuth' => $course_member,
        ]);
    }

    public function memberProgress(Course $course, CourseMember $course_member)
    {
        // return Inertia::render('Learn/Course/Member/MemberProgress', [
        return Inertia::render('Learn/Course/Member/MemberSettings', [
            'isCourseAdmin' => $course->user_id === auth()->id(),
            'course'        => new CourseResource($course),
            'lessons'       => LessonResource::collection($course->courseLessons),
            'groups'        => CourseGroupResource::collection($course->courseGroups),
            'member'        => new CourseMemberResource($course_member),
            'assignments'       => AssignmentResource::collection($course->courseAssignments), 
            'quizzes'           => CourseQuizResource::collection($course->courseQuizzes),
            // 'courseMemberOfAuth' => $course->courseMembers()->where('id', $course_member->id)->first(),
            'courseMemberOfAuth' => $course_member,
        ]);
    }

    public function updateOrderNumber(Course $course, CourseMember $member, Request $request)
    {
        $member->update([
            'order_number' => $request->order_number,
        ]);

        return response()->json([
            'success' => true,
        ]);
    }


}
