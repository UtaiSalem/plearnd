<?php

namespace App\Http\Controllers\Learn\Student\HomeVisit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Student;
use App\Models\StudentHomeVisit;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    /**
     * Show teacher dashboard
     */
    public function dashboard()
    {
        if (!session('homevisit_authenticated') || session('homevisit_user_type') !== 'teacher') {
            return redirect()->route('homevisit.login');
        }

        // Get stats for dashboard
        $stats = [
            'total_students' => Student::count(),
            'total_visits' => StudentHomeVisit::count(),
            'visits_this_month' => StudentHomeVisit::whereMonth('visit_date', now()->month)
                ->whereYear('visit_date', now()->year)
                ->count(),
        ];

        // Get unique classrooms for filter
        $classrooms = Student::join('student_academic_info', 'students.id', '=', 'student_academic_info.student_id')
            ->where('student_academic_info.is_current', true)
            ->distinct()
            ->orderBy('student_academic_info.current_class')
            ->pluck('student_academic_info.current_class')
            ->filter()
            ->values(); // Convert to array

        return Inertia::render('Learn/Student/HomeVisit/Teacher/Dashboard', [
            'students' => (object)['data' => []], // Empty initial state
            'classrooms' => $classrooms,
            'stats' => $stats,
            'filters' => [],
        ]);
    }

    /**
     * Search for students
     */
    public function searchStudents(Request $request)
    {
        if (!session('homevisit_authenticated') || session('homevisit_user_type') !== 'teacher') {
            return redirect()->route('homevisit.login');
        }

        $query = Student::with(['academicInfo', 'contacts']);

        // Search functionality - comprehensive search with collation fixed
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('first_name_th', 'like', "%{$request->search}%")
                  ->orWhere('last_name_th', 'like', "%{$request->search}%")
                  ->orWhere('student_id', 'like', "%{$request->search}%")
                  ->orWhere('citizen_id', 'like', "%{$request->search}%")
                  ->orWhere('nickname', 'like', "%{$request->search}%")
                  // Now we can search across tables since collations match
                  ->orWhereHas('academicInfo', function($subQ) use ($request) {
                      $subQ->where('school_name', 'like', "%{$request->search}%")
                           ->orWhere('current_class', 'like', "%{$request->search}%");
                  });
            });
        }

        // Filter by classroom through academic info
        if ($request->classroom) {
            $query->whereHas('academicInfo', function($q) use ($request) {
                $q->where('current_class', $request->classroom);
            });
        }

        $students = $query->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(15)
            ->withQueryString();

        // Get unique classrooms for filter
        $classrooms = Student::join('student_academic_info', 'students.id', '=', 'student_academic_info.student_id')
            ->where('student_academic_info.is_current', true)
            ->distinct()
            ->orderBy('student_academic_info.current_class')
            ->pluck('student_academic_info.current_class')
            ->filter()
            ->values(); // Convert to array

        // Get stats for dashboard
        $stats = [
            'total_students' => Student::count(),
            'total_visits' => StudentHomeVisit::count(),
            'visits_this_month' => StudentHomeVisit::whereMonth('visit_date', now()->month)
                ->whereYear('visit_date', now()->year)
                ->count(),
        ];

        return Inertia::render('Learn/Student/HomeVisit/Teacher/Dashboard', [
            'students' => $students,
            'classrooms' => $classrooms,
            'stats' => $stats,
            'filters' => $request->only(['search', 'classroom']),
        ]);
    }

    /**
     * Show student management page
     */
    public function manageStudent($studentId)
    {
        if (!session('homevisit_authenticated') || session('homevisit_user_type') !== 'teacher') {
            return redirect()->route('homevisit.login');
        }

        $student = Student::with(['academicInfo', 'addresses', 'contacts', 'guardians.contacts', 'healthInfo'])
            ->findOrFail($studentId);
        
        // Get student's home visit records
        $homeVisits = StudentHomeVisit::where('student_id', $studentId)
            ->orderBy('visit_date', 'desc')
            ->get();

        return Inertia::render('HomeVisit/Teacher/ManageStudent', [
            'student' => $student,
            'homeVisits' => $homeVisits
        ]);
    }

    /**
     * Create new home visit record
     */
    public function createHomeVisit(Request $request, $studentId)
    {
        if (!session('homevisit_authenticated') || session('homevisit_user_type') !== 'teacher') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $student = Student::with(['academicInfo', 'addresses', 'contacts'])->findOrFail($studentId);

        $validatedData = $request->validate([
            'visit_date' => 'required|date',
            'visit_purpose' => 'required|string|max:255',
            'visit_notes' => 'nullable|string',
            'teacher_name' => 'required|string|max:255',
        ]);

        try {
            // Create home visit record
            $homeVisit = StudentHomeVisit::create([
                'student_id' => $studentId,
                'visit_date' => $validatedData['visit_date'],
                'visit_purpose' => $validatedData['visit_purpose'],
                'visit_notes' => $validatedData['visit_notes'],
                'teacher_name' => $validatedData['teacher_name'],
                'visit_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->back()->with('success', 'บันทึกข้อมูลการเยี่ยมบ้านสำเร็จแล้ว');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Update existing home visit record
     */
    public function updateHomeVisit(Request $request, $homeVisitId)
    {
        if (!session('homevisit_authenticated') || session('homevisit_user_type') !== 'teacher') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $homeVisit = StudentHomeVisit::findOrFail($homeVisitId);
        
        // Similar validation and update logic as createHomeVisit
        $validatedData = $request->validate([
            'recommendations' => 'nullable|string',
            'follow_up' => 'nullable|string',
            'next_visit' => 'nullable|date',
            // Add other fields as needed
        ]);

        try {
            $homeVisit->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'อัปเดตข้อมูลการเยี่ยมบ้านสำเร็จแล้ว',
                'homeVisit' => $homeVisit
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete home visit record
     */
    public function deleteHomeVisit($homeVisitId)
    {
        if (!session('homevisit_authenticated') || session('homevisit_user_type') !== 'teacher') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $homeVisit = StudentHomeVisit::findOrFail($homeVisitId);
        
        try {
            // Delete associated photos
            if ($homeVisit->photos) {
                $photos = json_decode($homeVisit->photos, true);
                foreach ($photos as $photoPath) {
                    Storage::disk('public')->delete($photoPath);
                }
            }

            $homeVisit->delete();

            return response()->json([
                'success' => true,
                'message' => 'ลบข้อมูลการเยี่ยมบ้านสำเร็จแล้ว'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $e->getMessage()
            ], 500);
        }
    }
}
