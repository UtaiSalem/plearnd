# Play-Learn-Earn Resource Structure

This document outlines the reorganized resource structure based on the Play-Learn-Earn concept.

## Folder Structure

### Play (Social Network & Games)
Resources related to social networking, posts, interactions, and community features:

- **ActivityResource.php** - User activities and social interactions
- **PostResource.php** - Social media posts
- **PostCommentResource.php** - Comments on posts
- **PostImageResource.php** - Images attached to posts
- **PostImageCommentResource.php** - Comments on post images
- **PollResource.php** - Polls and surveys
- **FriendResource.php** - Friend relationships
- **FriendshipResource.php** - Friendship management

### Learn (Education & Learning)
Resources related to educational content, courses, and learning management:

- **AcademyResource.php** - Educational academies/institutions
- **AcademyAdminResource.php** - Academy administrators
- **AcademyMemberResource.php** - Academy membership
- **AcademyPostResource.php** - Posts within academies
- **AcademyPostCommentResource.php** - Comments on academy posts
- **AcademyPostImageResource.php** - Images in academy posts
- **AcademyPostImageCommentResource.php** - Comments on academy post images
- **CourseResource.php** - Educational courses
- **CourseAdminResource.php** - Course administrators
- **CourseMemberResource.php** - Course membership
- **CourseGroupResource.php** - Groups within courses
- **CourseGroupMemberResource.php** - Group membership
- **CoursePostResource.php** - Posts within courses
- **CoursePostCommentResource.php** - Comments on course posts
- **CoursePostImageResource.php** - Images in course posts
- **CoursePostImageCommentResource.php** - Comments on course post images
- **CourseQuizResource.php** - Course quizzes and tests
- **CourseQuizResultResource.php** - Quiz results and scores
- **AssignmentResource.php** - Course assignments
- **AssignmentAnswerResource.php** - Assignment submissions
- **LessonResource.php** - Individual lessons within courses
- **LessonCommentResource.php** - Comments on lessons
- **QuestionResource.php** - Quiz and assignment questions
- **QuestionOptionResource.php** - Options for multiple choice questions
- **TopicResource.php** - Discussion topics
- **TopicImageResource.php** - Images for topics
- **AttendanceDetailResource.php** - Attendance tracking details
- **CourseMemberGradeProgressResource.php** - Member grade progress tracking
- **CourseAttendanceResource.php** - Course attendance tracking

### Earn (Income Generation)
Resources related to monetization, donations, and revenue generation:

- **DonateResource.php** - Donation management
- **DonateRecipientResource.php** - Donation recipients

### Shared (Common Resources)
Resources used across multiple categories or core system functionality:

- **UserResource.php** - Core user resource
- **SupportResource.php** - Customer support system

## Namespace Updates

All moved resources have been updated with proper namespaces:

- Play resources: `namespace App\Http\Resources\Play;`
- Learn resources: `namespace App\Http\Resources\Learn;`
- Earn resources: `namespace App\Http\Resources\Earn;`
- Shared resources: `namespace App\Http\Resources\Shared;`

## Cross-References

Resources have been updated to reference other models using their new namespaces with full paths (e.g., `\App\Models\Play\Post::class`) to avoid conflicts.

## Benefits of This Structure

1. **Clear Separation**: Resources are organized by their primary function within the Play-Learn-Earn ecosystem
2. **Maintainability**: Easier to locate and modify resources based on their purpose
3. **Scalability**: New resources can be easily categorized into appropriate folders
4. **Team Collaboration**: Developers can focus on specific functional areas
5. **Code Organization**: Reduces cognitive load when working with related functionality

## Migration Notes

- Original resources remain in `app/Http/Resources/` for backward compatibility (only TopicResourse.php remains)
- New organized copies are in their respective subfolders
- All namespace references have been updated in the moved files
- Cross-resource relationships use fully qualified class names to avoid conflicts
- Academy and all related resources are now properly categorized under Learn as requested