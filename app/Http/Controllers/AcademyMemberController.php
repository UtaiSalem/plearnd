<?php

namespace App\Http\Controllers;

use App\Models\Academy;
use Illuminate\Http\Request;
use App\Models\AcademyMember;

class AcademyMemberController extends Controller
{
    public function store(Academy $academy)
    {        
        $academy->members()->toggle(auth()->id());
        $isMember = $academy->isMember(auth()->user());
        $isMember ? $academy->increment('total_students'): $academy->decrement('total_students');

        return response()->json([
            'success' => true,
            'isMember' => $isMember,
            'totalStudents' => $academy->total_students,
        ], 200);
    }

}
