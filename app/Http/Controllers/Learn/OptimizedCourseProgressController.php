<?php

namespace App\Http\Controllers\Learn;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMember;
use App\Models\CourseGroup;
use App\Models\Assignment;
use App\Models\CourseQuiz;
use App\Models\AssignmentAnswer;
use App\Models\CourseQuizResult;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
class OptimizedCourseProgressController extends Controller
{
    /**
     * Display the course progress page with optimized loading
     */
    public function index(Course $course)
    {
        // Use cache for static course data
        $cacheKey = 'course_progress_data_' . $course->id . '_' . auth()->id();
        $cachedData = Cache::remember($cacheKey, 300, function () use ($course) {
            return [
                'course' => $this->getOptimizedCourseData($course),
                'groups' => $this->getOptimizedCourseGroups($course),
                'assignments' => $this->getOptimizedAssignments($course),
                'quizzes' => $this->getOptimizedQuizzes($course),
            ];
        });

        // Get initial page of members (smaller batch size)
        $members = $this->getCourseMembers($course, 1, 20);
        
        // Get course member of auth user
        $courseMemberOfAuth = $course->courseMembers()
            ->where('user_id', auth()->id())
            ->with(['group'])
            ->first();

        return Inertia::render('Learn/Course/Progress/OptimizedCourseMembersProgress', [
            'course' => $cachedData['course'],
            'groups' => $cachedData['groups'],
            'assignments' => $cachedData['assignments'],
            'quizzes' => $cachedData['quizzes'],
            'initialMembers' => $members['data'],
            'initialPagination' => [
                'current_page' => $members['current_page'],
                'last_page' => $members['last_page'],
                'total' => $members['total'],
            ],
            'isCourseAdmin' => $course->user_id === auth()->id(),
            'courseMemberOfAuth' => $courseMemberOfAuth,
        ]);
    }

    /**
     * Get paginated course members for infinite scroll
     */
    public function getPaginatedMembers(Request $request, Course $course)
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 20);
        $groupId = $request->get('group_id', null);
        
        $members = $this->getCourseMembers($course, $page, $perPage, $groupId);
        
        return response()->json([
            'success' => true,
            'members' => $members['data'],
            'pagination' => [
                'current_page' => $members['current_page'],
                'last_page' => $members['last_page'],
                'total' => $members['total'],
                'has_more' => $members['current_page'] < $members['last_page'],
            ]
        ]);
    }

    /**
     * Get optimized course data
     */
    private function getOptimizedCourseData(Course $course)
    {
        return [
            'id' => $course->id,
            'name' => $course->name,
            'total_score' => $course->total_score ?? 0,
            'user_id' => $course->user_id,
        ];
    }

    /**
     * Get optimized course groups
     */
    private function getOptimizedCourseGroups(Course $course)
    {
        return $course->courseGroups()
            ->withCount('members')
            ->select('id', 'name', 'course_id')
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Get optimized assignments
     */
    private function getOptimizedAssignments(Course $course)
    {
        return $course->courseAssignments()
            ->select('id', 'title', 'points', 'course_id')
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Get optimized quizzes
     */
    private function getOptimizedQuizzes(Course $course)
    {
        return $course->courseQuizzes()
            ->select('id', 'title', 'total_score', 'course_id')
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Get course members with optimized queries
     */
    private function getCourseMembers(Course $course, $page = 1, $perPage = 20, $groupId = null)
    {
        $query = $course->courseMembers()
            ->with([
                'user' => function($query) {
                    $query->select('id', 'name', 'profile_photo_url');
                },
                'group' => function($query) {
                    $query->select('id', 'name');
                }
            ])
            ->select('id', 'user_id', 'course_id', 'member_code', 'order_number', 'achieved_score', 'bonus_points', 'grade_progress', 'notes_comments', 'group_id');
        
        // Filter by group if specified
        if ($groupId !== null) {
            $query->where('group_id', $groupId);
        }
        
        $members = $query->orderBy('order_number', 'asc')
            ->orderBy('created_at', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);
        
        // Pre-load assignment answers for all members to reduce queries
        $memberIds = $members->pluck('id')->toArray();
        $assignmentIds = $course->courseAssignments()->pluck('id')->toArray();
        
        $assignmentAnswers = AssignmentAnswer::whereIn('course_member_id', $memberIds)
            ->whereIn('assignment_id', $assignmentIds)
            ->with(['student' => function($query) {
                $query->select('id', 'name');
            }])
            ->select('id', 'course_member_id', 'assignment_id', 'points', 'student_id')
            ->get()
            ->groupBy('course_member_id');
        
        // Pre-load quiz results for all members
        $quizIds = $course->courseQuizzes()->pluck('id')->toArray();
        
        $quizResults = CourseQuizResult::whereIn('course_member_id', $memberIds)
            ->whereIn('course_quiz_id', $quizIds)
            ->select('id', 'course_member_id', 'course_quiz_id', 'score')
            ->get()
            ->groupBy('course_member_id');
        
        // Attach answers and results to members
        $members->getCollection()->transform(function ($member) use ($assignmentAnswers, $quizResults) {
            $member->assignment_answers = $assignmentAnswers->get($member->id, collect());
            $member->quiz_results = $quizResults->get($member->id, collect());
            return $member;
        });
        
        return $members->toArray();
    }

    /**
     * Update member bonus points
     */
    public function updateBonusPoints(Request $request, Course $course, CourseMember $member)
    {
        $request->validate([
            'bonus_points' => 'required|integer|min:0',
        ]);

        $member->update([
            'bonus_points' => $member->bonus_points + $request->bonus_points,
        ]);

        // Clear relevant cache
        $this->clearCourseProgressCache($course);

        return response()->json([
            'success' => true,
            'bonus_points' => $member->bonus_points,
        ]);
    }

    /**
     * Update member grade progress
     */
    public function updateGradeProgress(Request $request, Course $course, CourseMember $member)
    {
        $request->validate([
            'grade_progress' => 'required|integer|min:0|max:5',
        ]);

        $member->update([
            'grade_progress' => $request->grade_progress,
        ]);

        // Clear relevant cache
        $this->clearCourseProgressCache($course);

        return response()->json([
            'success' => true,
            'grade_progress' => $member->grade_progress,
        ]);
    }

    /**
     * Update member order number
     */
    public function updateOrderNumber(Request $request, Course $course, CourseMember $member)
    {
        $request->validate([
            'order_number' => 'required|integer|min:0',
        ]);

        $member->update([
            'order_number' => $request->order_number,
        ]);

        // Clear relevant cache
        $this->clearCourseProgressCache($course);

        return response()->json([
            'success' => true,
            'order_number' => $member->order_number,
        ]);
    }

    /**
     * Update member notes/comments
     */
    public function updateNotesComments(Request $request, Course $course, CourseMember $member)
    {
        $request->validate([
            'notes_comments' => 'nullable|string',
        ]);

        $member->update([
            'notes_comments' => $request->notes_comments,
        ]);

        // Clear relevant cache
        $this->clearCourseProgressCache($course);

        return response()->json([
            'success' => true,
            'notes_comments' => $member->notes_comments,
        ]);
    }

    /**
     * Set active group tab for a member
     */
    public function setActiveGroupTab(Request $request, Course $course, CourseMember $member)
    {
        $request->validate([
            'group_tab' => 'required|integer|min:0',
        ]);

        $member->update([
            'last_accessed_group_tab' => $request->group_tab,
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Clear course progress cache
     */
    private function clearCourseProgressCache(Course $course)
    {
        // Clear all course progress caches for this course
        $cachePattern = 'course_progress_data_' . $course->id . '_*';
        Cache::forget($cachePattern);
        
        // Clear member-specific caches
        $memberIds = $course->courseMembers()->pluck('id');
        foreach ($memberIds as $memberId) {
            Cache::forget('member_progress_' . $memberId);
        }
    }

    /**
     * Export course progress to Excel
     */
    public function exportToExcel(Request $request, Course $course)
    {
        $groupId = $request->get('group_id', null);
        
        // Get all members for export (no pagination)
        $members = $this->getCourseMembers($course, 1, 10000, $groupId);
        
        // Transform data for Excel export
        $exportData = [];
        
        // Header row
        $header = [
            'เลขที่', 'รหัส', 'ชื่อ - สกุล'
        ];
        
        // Add assignment columns
        foreach ($course->courseAssignments()->get() as $index => $assignment) {
            $header[] = '@' . ($index + 1) . '(' . $assignment->points . ')';
        }
        
        // Add quiz columns
        foreach ($course->courseQuizzes()->get() as $index => $quiz) {
            $header[] = '#' . ($index + 1) . '(' . $quiz->total_score . ')';
        }
        
        // Add other columns
        $header = array_merge($header, [
            'คะแนนเก็บ (' . ($course->total_score ?? 0) . ')',
            'คะแนนพิเศษ',
            'คะแนน (ได้/เต็ม)',
            'คะแนน (%)',
            'เกรดที่ทำได้',
            'เกรดที่แก้ได้',
            'หมายเหตุ'
        ]);
        
        $exportData[] = $header;
        
        // Data rows
        foreach ($members['data'] as $member) {
            $row = [
                $member['order_number'] ?? '',
                $member['member_code'] ?? '',
                $member['user']['name'] ?? ''
            ];
            
            // Add assignment scores
            foreach ($course->courseAssignments()->get() as $assignment) {
                $answer = $member['assignment_answers']->firstWhere('assignment_id', $assignment->id);
                $row[] = $answer ? $answer['points'] : '';
            }
            
            // Add quiz scores
            foreach ($course->courseQuizzes()->get() as $quiz) {
                $result = $member['quiz_results']->firstWhere('course_quiz_id', $quiz->id);
                $row[] = $result ? $result['score'] : '';
            }
            
            // Add other data
            $totalScore = $member['achieved_score'] + $member['bonus_points'];
            $percentage = $course->total_score > 0 ? round(($totalScore / $course->total_score) * 100) : 0;
            
            // Calculate grade
            $grade = 0;
            if ($percentage >= 80) $grade = 4;
            elseif ($percentage >= 75) $grade = 3.5;
            elseif ($percentage >= 70) $grade = 3;
            elseif ($percentage >= 65) $grade = 2.5;
            elseif ($percentage >= 60) $grade = 2;
            elseif ($percentage >= 55) $grade = 1.5;
            elseif ($percentage >= 50) $grade = 1;
            
            $row = array_merge($row, [
                $member['achieved_score'] ?? 0,
                $member['bonus_points'] ?? 0,
                $totalScore . '/' . ($course->total_score ?? 0),
                $percentage . '%',
                $grade,
                $member['grade_progress'] ?? 5,
                $member['notes_comments'] ?? ''
            ]);
            
            $exportData[] = $row;
        }
        
        return response()->json([
            'success' => true,
            'data' => $exportData,
        ]);
    }
}