<?php

namespace App\Http\Controllers;

use App\Models\Academy;
use Illuminate\Http\Request;
use App\Models\AcademyMember;

class AcademyMemberController extends Controller
{
    public function storemember(Academy $academy)
    {   
        if (auth()->user()->pp < $academy->membership_fees_points) {
            return response()->json([
                'success' => false,
                'msg'     => 'แต้มสะสมไม่เพียงพอ กรุณาเติมแต้มสะสมก่อนสมัครสมาชิก'
            ], 201);
        }

        $curent_member_status = AcademyMember::where('academy_id', $academy->id)->where('user_id', auth()->id())->first();

        if ($academy->academySetting->auto_accept_members === 1) {
            if (!$curent_member_status) {
                $newStatus = $academy->academyMembers()->create([
                    'user_id'   => auth()->id(),
                    'status'    => 2, 
                ]);
                $academy->increment('total_students');
            }
        }else {
            if (!$curent_member_status) {
                $newStatus = $academy->academyMembers()->create([
                    'user_id'   => auth()->id(),
                    'status'    => 1, 
                ]);
            }
        }
        
        // $academy->members()->toggle(auth()->id());
        // $isMember = $academy->isMember(auth()->user());
        // $isMember ? $academy->increment('total_students'): $academy->decrement('total_students');

        return response()->json([
            'success' => true,
            'memberStatus'  => $newStatus->status,
            'totalStudents' => $academy->total_students,
        ], 200);
    }


    public function unmember(Academy $academy)
    {   
        $auth_academy = AcademyMember::where('academy_id', $academy->id)->where('user_id', auth()->id())->first();
        if ($auth_academy->status ==='2') {
            $academy->decrement('total_students');
        }

        $academy->academyMembers()->delete();
        return response()->json([
            'success' => true,
        ], 200);
    }

}
