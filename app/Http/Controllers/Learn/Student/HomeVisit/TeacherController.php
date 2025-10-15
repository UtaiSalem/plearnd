<?php

namespace App\Http\Controllers\Learn\Student\HomeVisit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Student;
use App\Models\StudentHomeVisit;
use App\Models\HomeVisitImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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

        $query = Student::with([
            'academicInfo', 
            'addresses', 
            'contacts', 
            'guardians.contacts', 
            'healthInfo',
            'documents',
            'homeVisits' => function($q) {
                $q->orderBy('visit_date', 'desc')->with('images');
            }
        ]);

        // Search functionality - student ID only
        if ($request->search) {
            $query->where('student_id', 'like', "%{$request->search}%");
        }

        // Filter by classroom through academic info
        if ($request->classroom) {
            $query->whereHas('academicInfo', function($q) use ($request) {
                $q->where('current_class', $request->classroom);
            });
        }

        $students = $query->orderBy('first_name_th')
            ->orderBy('last_name_th')
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

        // Add home visits to student object
        $student->home_visits = $homeVisits;

        return Inertia::render('Learn/Student/HomeVisit/Teacher/ManageStudent', [
            'student' => $student
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

    /**
     * Update student information
     */
    public function updateStudent(Request $request, $studentId)
    {
        if (!session('homevisit_authenticated') || session('homevisit_user_type') !== 'teacher') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $student = Student::findOrFail($studentId);
        
        try {
            // Update student basic info
            $student->update($request->only([
                'title_prefix_th',
                'first_name_th', 
                'last_name_th',
                'nickname',
                'citizen_id',
                'class_level',
                'class_section'
            ]));

            // Update academic info if provided
            if ($request->academic_info) {
                $student->academicInfo()->updateOrCreate(
                    ['student_id' => $student->id],
                    $request->academic_info
                );
            }

            // Update contacts if provided
            if ($request->contacts) {
                // Simple approach: delete and recreate contacts
                $student->contacts()->delete();
                foreach ($request->contacts as $contact) {
                    if (!empty($contact['contact_value'])) {
                        $student->contacts()->create($contact);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'อัพเดทข้อมูลนักเรียนสำเร็จแล้ว'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการอัพเดทข้อมูล: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new home visit with images
     */
    public function createHomeVisitWithImages(Request $request, $studentId)
    {
        if (!session('homevisit_authenticated') || session('homevisit_user_type') !== 'teacher') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'visit_date' => 'required|date',
            'visit_time' => 'nullable',
            'notes' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $student = Student::findOrFail($studentId);
        
        try {
            // Create home visit record
            $homeVisit = StudentHomeVisit::create([
                'student_id' => $student->id,
                'visit_date' => $request->visit_date,
                'visit_time' => $request->visit_time,
                'notes' => $request->notes,
                'teacher_name' => session('homevisit_user_name', 'ครู'),
                'created_by' => session('homevisit_user_id'),
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $imageName = time() . '_' . $index . '.' . $image->getClientOriginalExtension();
                    $imagePath = $image->storeAs('home_visit_images/' . $homeVisit->id, $imageName, 'public');
                    
                    // Save using HomeVisitImage model
                    HomeVisitImage::create([
                        'home_visit_id' => $homeVisit->id,
                        'image_path' => $imagePath,
                        'image_name' => $imageName,
                        'image_type' => 'evidence',
                        'description' => 'รูปภาพหลักฐานการเยี่ยมบ้าน',
                        'uploaded_by' => session('homevisit_user_id', 1)
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'บันทึกการเยี่ยมบ้านสำเร็จแล้ว'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()
            ], 500);
        }
    }
}
