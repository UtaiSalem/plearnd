<?php

namespace App\Http\Controllers\Learn\Course\Attendances;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\Learn\Course\info\Course;
use App\Models\Learn\Course\groups\CourseGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Learn\Course\attendances\CourseAttendance;
use App\Http\Resources\Learn\info\CourseResource;
use App\Http\Resources\Learn\groups\CourseGroupResource;
use App\Http\Resources\Learn\attendances\CourseAttendanceResource;

class CourseAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course)
    {
        $courseMemberOfAuth = $course->courseMembers()->where('user_id', auth()->id())->first();
        
        // Initialize attendances as empty collection with pagination structure
        $attendances = [
            'data' => [],
            'total' => 0
        ];
        
        // Only get attendances if the member has a group
        if ($courseMemberOfAuth && $courseMemberOfAuth->group_id) {
            // Get ALL attendance sessions for the member's group
            $groupAttendances = $course->courseAttendances()->where('group_id', $courseMemberOfAuth->group_id)->get();
            
            // Transform with CourseAttendanceResource (includes auth attendance status) and convert to array
            $attendancesData = CourseAttendanceResource::collection($groupAttendances)->toArray(request());
            
            $attendances = [
                'data' => $attendancesData,
                'total' => $groupAttendances->count()
            ];
        }
<?php

namespace App\Http\Controllers\Learn\Course\Attendances;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\Learn\Course\info\Course;
use App\Models\Learn\Course\groups\CourseGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Learn\Course\attendances\CourseAttendance;
use App\Http\Resources\Learn\info\CourseResource;
use App\Http\Resources\Learn\groups\CourseGroupResource;
use App\Http\Resources\Learn\attendances\CourseAttendanceResource;

class CourseAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course)
    {
        $courseMemberOfAuth = $course->courseMembers()->where('user_id', auth()->id())->first();
        
        // Initialize attendances as empty collection with pagination structure
        $attendances = [
            'data' => [],
            'total' => 0
        ];
        
            'late_time'    => $request->late_time,
            'description'   => $request->description,
        ]);

        return response()->json([
            'success'       => true,
            'attendance'    => new CourseAttendanceResource($attendance),
        ], 200);

    }

    // Update Attendance
    public function update(CourseAttendance $attendance, Request $request)
    {
        $request->validate([
            'start_at' => 'required|date',
            'finish_at' => 'required|date',
            'late_time' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        $attendance->update([
            'date'          => Carbon::parse($request->start_at)->setTimezone('Asia/Bangkok')->format('Y-m-d H:i:s'),
            'start_at'      => Carbon::parse($request->start_at)->setTimezone('Asia/Bangkok')->format('Y-m-d H:i:s'),
            'finish_at'     => Carbon::parse($request->finish_at)->setTimezone('Asia/Bangkok')->format('Y-m-d H:i:s'),
            'late_time'    => $request->late_time,
            'description'   => $request->description,
        ]);

        return response()->json([
            'success'       => true,
            'attendance'    => new CourseAttendanceResource($attendance),
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseAttendance $attendance)
    {
        //delete attendance details
        $attendance->details()->delete();
        //delete attendance
        $attendance->delete();

        return response()->json([
            'success' => true,
        ], 200);
    }

    // Get Course Attendance by Course and CourseGroup
    public function getCourseGroupAttendances(Course $course, CourseGroup $group, Request $request)
    {
        return response()->json([
            'success' => true,
            'attendances'   => CourseAttendanceResource::collection($course->courseAttendances->where('group_id', $group->id))
        ], 200);
    }

}
