<?php

namespace App\Http\Controllers\Learn\Student\HomeVisit;

use App\Http\Controllers\Controller;
use App\Models\StudentHomeVisit;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        // Ensure admin is authenticated
        $this->middleware(function ($request, $next) {
            if (!session('homevisit_admin_authenticated')) {
                return redirect()->route('homevisit.login');
            }
            return $next($request);
        });
    }

    /**
     * Admin Dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_students' => Student::count(),
            'total_visits' => StudentHomeVisit::count(),
            'visits_this_month' => StudentHomeVisit::whereMonth('visit_date', now()->month)
                ->whereYear('visit_date', now()->year)
                ->count(),
            'pending_visits' => StudentHomeVisit::where('visit_status', 'pending')->count(),
            'completed_visits' => StudentHomeVisit::where('visit_status', 'completed')->count(),
        ];

        // Recent visits
        $recentVisits = StudentHomeVisit::with('student')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Monthly visit chart data
        $monthlyVisits = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyVisits[] = [
                'month' => $date->format('M Y'),
                'visits' => StudentHomeVisit::whereMonth('visit_date', $date->month)
                    ->whereYear('visit_date', $date->year)
                    ->count()
            ];
        }

        return Inertia::render('Learn/Student/HomeVisit/Admin/Dashboard', [
            'stats' => $stats,
            'recentVisits' => $recentVisits,
            'monthlyVisits' => $monthlyVisits,
        ]);
    }

    /**
     * Student Management
     */
    public function students(Request $request)
    {
        $query = Student::with(['academicInfo', 'contacts']);

        // Search functionality - include StudentCard search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('first_name_th', 'like', "%{$request->search}%")
                  ->orWhere('last_name_th', 'like', "%{$request->search}%")
                  ->orWhere('student_id', 'like', "%{$request->search}%")
                  ->orWhere('citizen_id', 'like', "%{$request->search}%")
                  // Also search by matching StudentCard data
                  ->orWhereIn('student_id', function($subquery) use ($request) {
                      $subquery->select('student_number')
                               ->from('student_cards')
                               ->where('first_name_thai', 'like', "%{$request->search}%")
                               ->orWhere('last_name_thai', 'like', "%{$request->search}%")
                               ->orWhere('student_number', 'like', "%{$request->search}%");
                  })
                  ->orWhereIn('citizen_id', function($subquery) use ($request) {
                      $subquery->select('national_id')
                               ->from('student_cards')
                               ->where('first_name_thai', 'like', "%{$request->search}%")
                               ->orWhere('last_name_thai', 'like', "%{$request->search}%")
                               ->orWhere('national_id', 'like', "%{$request->search}%");
                  });
            });
        }

        // Filter by classroom through academic info
        if ($request->classroom) {
            $query->whereHas('academicInfo', function($q) use ($request) {
                $q->where('classroom', $request->classroom);
            });
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $students = $query->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(20)
            ->withQueryString();

        // Get unique classrooms for filter from academic info
        $classrooms = Student::join('student_academic_info', 'students.id', '=', 'student_academic_info.student_id')
            ->distinct()
            ->orderBy('student_academic_info.classroom')
            ->pluck('student_academic_info.classroom')
            ->filter();

        return Inertia::render('HomeVisit/Admin/Students', [
            'students' => $students,
            'classrooms' => $classrooms,
            'filters' => $request->only(['search', 'classroom', 'status']),
        ]);
    }

    /**
     * View/Edit Student Details
     */
    public function showStudent($id)
    {
        $student = Student::with(['academicInfo', 'addresses', 'contacts', 'guardians.contacts', 'healthInfo'])
            ->findOrFail($id);
        
        $visits = StudentHomeVisit::where('student_id', $id)
            ->orderBy('visit_date', 'desc')
            ->get();

        return Inertia::render('HomeVisit/Admin/StudentDetail', [
            'student' => $student,
            'visits' => $visits,
        ]);
    }

    /**
     * Update Student Information
     */
    public function updateStudent(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'classroom' => 'required|string|max:20',
            'national_id' => 'nullable|string|max:13',
            'student_id' => 'nullable|string|max:20',
            'phone_number' => 'nullable|string|max:20',
            'house_number' => 'nullable|string|max:50',
            'subdistrict' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'guardian_full_name' => 'nullable|string|max:200',
            'guardian_phone_number' => 'nullable|string|max:20',
        ]);

        // Update student basic info
        $student->update([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'national_id' => $validatedData['national_id'],
            'student_id' => $validatedData['student_id'],
        ]);

        // Update academic info
        if ($student->academicInfo) {
            $student->academicInfo->update([
                'classroom' => $validatedData['classroom'],
            ]);
        }

        // Update contact info
        if ($student->contacts->isNotEmpty()) {
            $student->contacts->first()->update([
                'phone' => $validatedData['phone_number'],
            ]);
        }

        // Update address info
        if ($student->addresses->isNotEmpty()) {
            $student->addresses->first()->update([
                'house_number' => $validatedData['house_number'],
                'subdistrict' => $validatedData['subdistrict'],
                'district' => $validatedData['district'],
                'province' => $validatedData['province'],
                'postal_code' => $validatedData['postal_code'],
            ]);
        }

        return redirect()->back()->with('success', 'อัพเดทข้อมูลนักเรียนเรียบร้อยแล้ว');
    }

    /**
     * Home Visit Reports
     */
    public function visits(Request $request)
    {
        $query = StudentHomeVisit::with('student');

        // Date range filter
        if ($request->date_from) {
            $query->whereDate('visit_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('visit_date', '<=', $request->date_to);
        }

        // Status filter
        if ($request->status) {
            $query->where('visit_status', $request->status);
        }

        // Teacher filter
        if ($request->teacher) {
            $query->where('teacher_name', 'like', "%{$request->teacher}%");
        }

        // Classroom filter
        if ($request->classroom) {
            $query->whereHas('student.academicInfo', function($q) use ($request) {
                $q->where('classroom', $request->classroom);
            });
        }

        $visits = $query->orderBy('visit_date', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Get unique classrooms and teachers for filters
        $classrooms = Student::join('student_academic_info', 'students.id', '=', 'student_academic_info.student_id')
            ->distinct()
            ->orderBy('student_academic_info.classroom')
            ->pluck('student_academic_info.classroom')
            ->filter();

        $teachers = StudentHomeVisit::distinct()
            ->orderBy('teacher_name')
            ->pluck('teacher_name')
            ->filter();

        return Inertia::render('HomeVisit/Admin/Visits', [
            'visits' => $visits,
            'classrooms' => $classrooms,
            'teachers' => $teachers,
            'filters' => $request->only(['date_from', 'date_to', 'status', 'teacher', 'classroom']),
        ]);
    }

    /**
     * View Visit Details
     */
    public function showVisit($id)
    {
        $visit = StudentHomeVisit::with('student')->findOrFail($id);

        return Inertia::render('HomeVisit/Admin/VisitDetail', [
            'visit' => $visit,
        ]);
    }

    /**
     * Update Visit Status
     */
    public function updateVisitStatus(Request $request, $id)
    {
        $visit = StudentHomeVisit::findOrFail($id);

        $request->validate([
            'visit_status' => 'required|in:pending,in-progress,completed,cancelled',
            'admin_notes' => 'nullable|string',
        ]);

        $visit->update([
            'visit_status' => $request->visit_status,
            'admin_notes' => $request->admin_notes,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'อัพเดทสถานะการเยี่ยมบ้านเรียบร้อยแล้ว');
    }

    /**
     * Export Reports
     */
    public function exportVisits(Request $request)
    {
        $query = StudentHomeVisit::with('student');

        // Apply same filters as visits method
        if ($request->date_from) {
            $query->whereDate('visit_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('visit_date', '<=', $request->date_to);
        }
        if ($request->status) {
            $query->where('visit_status', $request->status);
        }

        $visits = $query->orderBy('visit_date', 'desc')->get();

        $filename = 'home-visits-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($visits) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV Header
            fputcsv($file, [
                'วันที่เยี่ยม',
                'ชื่อ-นามสกุลนักเรียน',
                'ชั้นเรียน',
                'ครูผู้เยี่ยม',
                'สถานะ',
                'หัวข้อการเยี่ยม',
                'สรุปผลการเยี่ยม'
            ]);

            foreach ($visits as $visit) {
                fputcsv($file, [
                    $visit->visit_date->format('d/m/Y'),
                    $visit->student->full_name_thai ?? 'N/A',
                    $visit->student->classroom ?? 'N/A',
                    $visit->teacher_name,
                    $visit->visit_status,
                    $visit->visit_purpose,
                    $visit->visit_summary,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Admin Settings
     */
    public function settings()
    {
        return Inertia::render('HomeVisit/Admin/Settings');
    }

    /**
     * Logout Admin
     */
    public function logout()
    {
        session()->forget('homevisit_admin_authenticated');
        return redirect()->route('homevisit.login')->with('success', 'ออกจากระบบเรียบร้อยแล้ว');
    }
}