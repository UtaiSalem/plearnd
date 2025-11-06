# Play-Learn-Earn Controller Structure

This document outlines the reorganized controller structure based on the Play-Learn-Earn concept.

## Folder Structure

### Play (Social Network & Games)
Controllers related to social networking, posts, interactions, and community features:

- **ActivityController.php** - User activities and social interactions
- **CommunityController.php** - Community groups and organizations
- **CommunityMembershipController.php** - User membership in communities
- **FriendController.php** - Friend relationships
- **ImageController.php** - General image handling
- **LikePostController.php** - Post likes
- **MessageController.php** - Private messaging system
- **NewsfeedController.php** - Newsfeed management
- **NotificationController.php** - User notifications
- **PollController.php** - Polls and surveys
- **PostController.php** - Social media posts (updated namespace)
- **PostCommentController.php** - Comments on posts
- **PostCommentReactionController.php** - Reactions on post comments
- **PostImageController.php** - Images attached to posts
- **PostImageCommentController.php** - Comments on post images
- **PostImageCommentReactionController.php** - Reactions on post image comments
- **PostImageReactionController.php** - Reactions on post images
- **PostReactionController.php** - General post reactions
- **CommentController.php** - General comment handling

### Learn (Education & Learning)
Controllers related to educational content, courses, and learning management:

- **AcademyController.php** - Educational academies/institutions (updated namespace)
- **AcademyAdminController.php** - Academy administrators
- **AcademyActivityController.php** - Academy activities
- **AcademyCourseController.php** - Courses within academies
- **AcademyGroupController.php** - Groups within academies
- **AcademyGroupAdminController.php** - Group administrators
- **AcademyGroupMemberController.php** - Group membership
- **AcademyMemberController.php** - Academy membership
- **AcademyPostController.php** - Posts within academies
- **AcademyPostCommentController.php** - Comments on academy posts
- **AcademyPostImageCommentController.php** - Comments on academy post images
- **AcademySettingController.php** - Academy configuration settings
- **AssignmentController.php** - Course assignments (updated namespace)
- **AssignmentAnswerController.php** - Assignment submissions
- **AssignmentImageController.php** - Images for assignments
- **AnswerImageController.php** - Images for assignment answers
- **AttendanceDetailController.php** - Attendance tracking details
- **CourseController.php** - Educational courses (updated namespace)
- **CourseActivityController.php** - Course activities
- **CourseAdminController.php** - Course administrators
- **CourseAssignmentController.php** - Course assignments
- **CourseAttendanceController.php** - Course attendance tracking
- **CourseGroupController.php** - Groups within courses
- **CourseGroupAdminController.php** - Group administrators
- **CourseGroupMemberController.php** - Group membership
- **CourseLessonController.php** - Course lessons
- **CourseLessonReactionController.php** - Reactions on course lessons
- **CourseMemberController.php** - Course membership
- **CoursePostController.php** - Posts within courses
- **CoursePostCommentController.php** - Comments on course posts
- **CoursePostCommentReactionController.php** - Reactions on course post comments
- **CoursePostImageController.php** - Images in course posts
- **CoursePostImageCommentController.php** - Comments on course post images
- **CoursePostImageCommentReactionController.php** - Reactions on course post image comments
- **CoursePostImageReactionController.php** - Reactions on course post images
- **CoursePostReactionController.php** - Reactions on course posts
- **CourseQuestionController.php** - Course questions
- **CourseQuizController.php** - Course quizzes and tests (updated namespace)
- **CourseQuizQuestionController.php** - Quiz questions
- **CourseQuizResultController.php** - Quiz results and scores
- **CourseSettingController.php** - Course configuration settings
- **LessonController.php** - Individual lessons within courses (updated namespace)
- **LessonAssignmentController.php** - Lesson assignments
- **LessonCommentController.php** - Comments on lessons
- **LessonCommentImageController.php** - Images in lesson comments
- **LessonCommentReactionController.php** - Reactions on lesson comments
- **LessonImageController.php** - Images for lessons
- **LessonQuestionController.php** - Lesson questions
- **MentalMathController.php** - Mental math exercises
- **QuestionController.php** - Quiz and assignment questions
- **QuestionAnswerController.php** - Question answers
- **QuestionImageController.php** - Images for questions
- **QuestionOptionController.php** - Options for multiple choice questions
- **TopicController.php** - Discussion topics
- **TopicAssignmentController.php** - Topic assignments
- **TopicImageController.php** - Images for topics
- **TopicQuestionController.php** - Topic questions
- **UserAnswerQuestionController.php** - User responses to questions

### Earn (Income Generation)
Controllers related to monetization, donations, and revenue generation:

- **DonateController.php** - Donation management (updated namespace)

### Shared (Common Controllers)
Controllers used across multiple categories or core system functionality:

- **DashboardController.php** - Main dashboard
- **ForgotPasswordController.php** - Password reset functionality
- **PlearndAdminController.php** - System administrators
- **SupportController.php** - Customer support system
- **SuggesterController.php** - Suggestion system
- **TestController.php** - Testing functionality
- **UserController.php** - Core user controller (updated namespace)
- **UserProfileController.php** - User profile management
- **VisitCounterController.php** - Visit tracking system
- **WelcomeController.php** - Welcome/Landing page

## Namespace Updates

All moved controllers have been updated with proper namespaces:

- Play controllers: `namespace App\Http\Controllers\Play;`
- Learn controllers: `namespace App\Http\Controllers\Learn;`
- Earn controllers: `namespace App\Http\Controllers\Earn;`
- Shared controllers: `namespace App\Http\Controllers\Shared;`

## Cross-References

Controllers have been updated to reference other models using their new namespaces with full paths (e.g., `\App\Models\Play\Post::class`) to avoid conflicts.

## Benefits of This Structure

1. **Clear Separation**: Controllers are organized by their primary function within the Play-Learn-Earn ecosystem
2. **Maintainability**: Easier to locate and modify controllers based on their purpose
3. **Scalability**: New controllers can be easily categorized into appropriate folders
4. **Team Collaboration**: Developers can focus on specific functional areas
5. **Code Organization**: Reduces cognitive load when working with related functionality

## Migration Notes

- Original controllers remain in `app/Http/Controllers/` for backward compatibility (Controller.php only)
- New organized copies are in their respective subfolders
- All namespace references have been updated in the moved files
- Cross-controller relationships use fully qualified class names to avoid conflicts
- Academy and all related controllers are now properly categorized under Learn as requested