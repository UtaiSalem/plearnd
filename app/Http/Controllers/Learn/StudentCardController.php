<?php

namespace App\Http\Controllers\Learn;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\StudentCard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class StudentCardController extends Controller
{
    //index method
    public function index() 
    {
        return Inertia::render('Learn/Student/StudentCardIndex');
    }

    public function adminIndex() 
    {
        return Inertia::render('Learn/Student/Admin/StudentCardIndex');
    }

    //getStudentByRoom method
    public function getStudentByRoom($level, $room)
    {
        // Logic to fetch students by level and room
        // This is a placeholder; actual implementation will depend on your data source
        // $students = []; // Fetch students from the database or any other source

        $students = StudentCard::where('student_class', $level)->where('student_section', $room)->get();

        return Inertia::render('Learn/Student/StudentCardByRoom', [
            'students' => $students,
            'level' => $level,
            'room' => $room,
        ]);
    }

        public function adminGetStudentByRoom($level, $room)
    {
        // Logic to fetch students by level and room
        // This is a placeholder; actual implementation will depend on your data source
        // $students = []; // Fetch students from the database or any other source

        $students = StudentCard::where('student_class', $level)->where('student_section', $room)->get();

        return Inertia::render('Learn/Student/Admin/StudentCardByRoom', [
            'students' => $students,
            'level' => $level,
            'room' => $room,
        ]);
    }

    public function updateImage(StudentCard $student_card, Request $request)
    {
        // Validate request
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        try {
            // Handle image upload
            if ($request->hasFile('photo')) {
                // Delete old image if exists
                if ($student_card->student_image) {
                    Storage::disk('public')->delete('images/students/'.$student_card->student_class.'/'.$student_card->student_section.'/'.$student_card->student_image);
                }

                // Store new image
                // $path = $request->file('photo')->store('students', 'public');
                $fileName = $student_card->student_id. '.' . $request->file('photo')->getClientOriginalExtension();
                
                // Store the image in the public disk under 'images/students' directory
                // $path = $request->file('photo')->storeAs('images/students', $fileName, 'public');
                $path = Storage::disk('public')->putFileAs('images/students/'.$student_card->student_class.'/'.$student_card->student_section, $request->file('photo'), $fileName);


                // Update student card record
                $student_card->update([
                    'student_image' => $fileName
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Image uploaded successfully',
                    'path' => Storage::url($path)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No image file provided'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStudentID(StudentCard $student_card, Request $request)
    {
        // Validate request
        $request->validate([
            'student_id' => 'required|string|max:255',
        ]);

        try {
            // Update student card record
            $student_card->update([
                'student_id' => $request->input('student_id'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student ID updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update student ID',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function updateStudentNameTh(StudentCard $student_card, Request $request)
    {
        // Validate request
        $request->validate([
            'student_name_th' => 'nullable|string|max:255',
        ]);

        try {
            // Update student card record
            $student_card->update([
                'student_name_th' => $request->input('student_name_th'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student name (Thai) updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update student name (Thai)',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function updateStudentNameEn(StudentCard $student_card, Request $request)
    {
        // Validate request
        $request->validate([
            'student_name_en' => 'nullable|string|max:255',
        ]);

        try {
            // Update student card record
            $student_card->update([
                'student_name_en' => $request->input('student_name_en'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student name (English) updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update student name (English)',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(StudentCard $student, Request $request)
    {
        // Validate request
        $request->validate([
            'student_id' => 'nullable|string|max:255',
            'prefix_name' => 'nullable|string|max:50',
            'student_name_th' => 'nullable|string|max:255',
            'last_name_th' => 'nullable|string|max:255',
            'student_name_en' => 'nullable|string|max:255',
            'id_card_no' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable',
        ]);
 
        try {
            // Update student card record
            $student->update([
                'student_id' => $request->input('student_id'),
                'prefix_name' => $request->input('prefix_name'),
                'student_name_th' => $request->input('student_name_th'),
                'last_name_th' => $request->input('last_name_th'),
                'student_name_en' => $request->input('student_name_en'),
                'id_card_no' => $request->input('id_card_no'),
                'date_of_birth' => $request->input('date_of_birth'),
                'date_of_birth_str' => $this->convertDateToThaiFormat($request->input('date_of_birth')),
                'student_fullname_th' => $request->input('prefix_name') . ' ' . $request->input('student_name_th') . ' ' . $request->input('last_name_th'),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Student card updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update student card',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function convertDateToThaiFormat($date)
    {
        if (!$date) {
            return null;
        }

        // Convert date to Carbon instance
        $carbonDate = Carbon::createFromFormat('Y-m-d', $date);

        // Return date in Thai format (m/d/Y)
        return $carbonDate->format('m/d/Y');
    }

    // destroyPhoto method
    public function destroyPhoto(StudentCard $student)
    {
        try {
            // Check if the student has a photo
            if ($student->student_image) {
                // Delete the photo from storage
                Storage::disk('public')->delete('images/students/'.$student->student_class.'/'.$student->student_section.'/'.$student->student_image);

                // Update the student record to remove the photo
                $student->update(['student_image' => null]);

                return response()->json([
                    'success' => true,
                    'message' => 'Photo deleted successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No photo found for this student'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete photo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}