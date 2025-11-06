<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\CourseMember;
use Illuminate\Http\Request;
use App\Models\CourseGroupMember;
use App\Http\Resources\CourseGroupResource;
use Illuminate\Support\Facades\DB;

class CourseGroupMemberController extends Controller
{


    /**
     * Store a newly created resource in storage.
     */
    public function store(Course $course, CourseGroup $group)
    {
        // ตรวจสอบแต้มสะสม
        if (auth()->user()->pp < $course->tuition_fees) {
            return response()->json([
                'success' => false,
                'msg'     => 'แต้มสะสมไม่เพียงพอ กรุณาเติมแต้มสะสมก่อนสมัครสมาชิก'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $course_member = CourseMember::where('course_id', $course->id)
                                        ->where('user_id', auth()->id())
                                        ->first();

            $isNewMember = false;
            $wasNotApproved = false;

            if (!$course_member) {
                // กรณีที่ยังไม่เคยเป็นสมาชิกรายวิชา - สร้างใหม่
                $isNewMember = true;
                
                $new_course_member = new CourseMember();
                $new_course_member->user_id                 = auth()->id();
                $new_course_member->course_id               = $course->id;
                $new_course_member->course_member_status    = $course->courseSettings->auto_accept_members;
                $new_course_member->group_id                = $group->id;
                $new_course_member->group_member_status     = $group->auto_accept_member;
                $new_course_member->save();
                $new_course_member->refresh();

                // สร้างหรืออัพเดท CourseGroupMember
                $courseGroupMember = CourseGroupMember::updateOrCreate(
                    [
                        'user_id'    => auth()->id(),
                        'course_id'  => $new_course_member->course_id,
                    ],
                    [
                        'group_id'   => $group->id,
                        'status'     => $group->auto_accept_member,
                    ]
                );

                // อัพเดทจำนวน enrolled_students ถ้าได้รับการอนุมัติทั้งคอร์สและกลุ่ม
                if ($new_course_member->course_member_status == 1 && $courseGroupMember->status == 1) {
                    $course->increment('enrolled_students');
                }

                $course_member = $new_course_member;
                
            } else {
                // กรณีที่เป็นสมาชิกรายวิชาอยู่แล้ว - อัพเดทข้อมูล
                
                // เก็บสถานะเดิมเพื่อตรวจสอบการเปลี่ยนแปลง
                $oldCourseMemberStatus = $course_member->course_member_status;
                $oldGroupMemberStatus = $course_member->group_member_status;

                // อัพเดทข้อมูลสมาชิกรายวิชา
                $course_member->course_member_status    = $course->courseSettings->auto_accept_members;
                $course_member->group_id                = $group->id;
                $course_member->group_member_status     = $group->auto_accept_member;
                $course_member->save();
                $course_member->refresh();

                // อัพเดท CourseGroupMember
                $courseGroupMember = CourseGroupMember::updateOrCreate(
                    [
                        'user_id'    => auth()->id(),
                        'course_id'  => $course->id,
                    ],
                    [
                        'group_id'   => $group->id,
                        'status'     => $group->auto_accept_member,
                    ]
                );

                // ตรวจสอบว่าเปลี่ยนจากไม่อนุมัติเป็นอนุมัติหรือไม่
                if (($oldCourseMemberStatus == 0 || $oldGroupMemberStatus == 0) && 
                    ($course_member->course_member_status == 1 && $courseGroupMember->status == 1)) {
                    $wasNotApproved = true;
                    $course->increment('enrolled_students');
                }
            }

            DB::commit();

            // รีเฟรชข้อมูลคอร์สเพื่อให้ได้ค่า enrolled_students ล่าสุด
            $course->refresh();

            return response()->json([
                'success'               => true,
                'courseMemberOfAuth'    => $course->courseMembers()->where('user_id', auth()->id())->first(),
                'group'                 => new CourseGroupResource($group),
                'course'                => [
                    'enrolled_students' => $course->enrolled_students,
                ],
                'message'               => $isNewMember 
                    ? 'ขอเป็นสมาชิกกลุ่มเรียบร้อยแล้ว' 
                    : 'เปลี่ยนกลุ่มเรียบร้อยแล้ว'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'msg'     => 'เกิดข้อผิดพลาดในการขอเข้าร่วมกลุ่ม: ' . $e->getMessage()
            ], 500);
        }
    }



    public function unMemberGroup(Course $course, CourseGroup $group, CourseMember $member)
    {
        try {
            DB::beginTransaction();

            // เก็บสถานะเดิมเพื่อตรวจสอบว่าต้องลดจำนวน enrolled_students หรือไม่
            $wasActiveMember = ($member->course_member_status == 1 && $member->group_member_status == 1);

            // อัพเดทข้อมูลสมาชิกรายวิชา
            $member->group_id               = null;
            $member->group_member_status    = 0;
            $member->save();
            $member->refresh();

            // อัพเดท CourseGroupMember
            $courseGroupMember = CourseGroupMember::where('group_id', $group->id)
                                                  ->where('user_id', auth()->id())
                                                  ->first();
            
            if ($courseGroupMember) {
                $courseGroupMember->group_id    = null;
                $courseGroupMember->status      = 0;
                $courseGroupMember->save();
            }

            // ลดจำนวน enrolled_students ถ้าเคยเป็นสมาชิกที่ active
            if ($wasActiveMember && $course->enrolled_students > 0) {
                $course->decrement('enrolled_students');
            }

            DB::commit();

            // รีเฟรชข้อมูลคอร์สเพื่อให้ได้ค่า enrolled_students ล่าสุด
            $course->refresh();

            return response()->json([
                'success'       => true,
                'courseMember'  => $member,
                'group'         => new CourseGroupResource($group),
                'course'        => [
                    'enrolled_students' => $course->enrolled_students,
                ],
                'message'       => 'ออกจากกลุ่มเรียบร้อยแล้ว'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'msg'     => 'เกิดข้อผิดพลาดในการออกจากกลุ่ม: ' . $e->getMessage()
            ], 500);
        }
    }
}
