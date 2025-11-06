# Play-Learn-Earn Model Structure

This document outlines the reorganized model structure based on the Play-Learn-Earn concept.

## Folder Structure

### Play (Social Network & Games)
Models related to social networking, posts, interactions, and community features:

- **Activity.php** - User activities and social interactions
- **Community.php** - Community groups and organizations
- **CommunityMembership.php** - User membership in communities
- **DislikedPost.php** - Post dislikes
- **Friend.php** - Friend relationships
- **Friendship.php** - Friendship management
- **LikedPost.php** - Post likes
- **Message.php** - Private messaging system
- **Notification.php** - User notifications
- **Poll.php** - Polls and surveys
- **Post.php** - Social media posts (updated namespace)
- **PostComment.php** - Comments on posts
- **PostImage.php** - Images attached to posts
- **PostImageComment.php** - Comments on post images
- **PostImageDislike.php** - Dislikes on post images
- **PostImageLike.php** - Likes on post images

### Learn (Education & Learning)
Models related to educational content, courses, and learning management:

- **Academy.php** - Educational academies/institutions (updated namespace)
- **AcademyAdmin.php** - Academy administrators
- **AcademyGroup.php** - Groups within academies
- **AcademyGroupAdmin.php** - Group administrators
- **AcademyGroupMember.php** - Group membership
- **AcademyMember.php** - Academy membership
- **AcademyPost.php** - Posts within academies
- **AcademyPostComment.php** - Comments on academy posts
- **AcademyPostCommentImage.php** - Images in academy post comments
- **AcademyPostDislike.php** - Dislikes on academy posts
- **AcademyPostImage.php** - Images in academy posts
- **AcademyPostImageComment.php** - Comments on academy post images
- **AcademyPostImageDislike.php** - Dislikes on academy post images
- **AcademyPostImageLike.php** - Likes on academy post images
- **AcademyPostLike.php** - Likes on academy posts
- **AcademySetting.php** - Academy configuration settings
- **AnswerImage.php** - Images for assignment answers
- **Assignment.php** - Course assignments (updated namespace)
- **AssignmentAnswer.php** - Assignment submissions
- **AssignmentAnswerImage.php** - Images in assignment answers
- **AssignmentImage.php** - Images for assignments
- **AttendanceDetail.php** - Attendance tracking details
- **Course.php** - Educational courses (updated namespace)
- **CourseAdmin.php** - Course administrators
- **CourseAttendance.php** - Course attendance tracking
- **CourseGroup.php** - Groups within courses
- **CourseGroupAdmin.php** - Group administrators
- **CourseGroupMember.php** - Group membership
- **CourseMember.php** - Course membership
- **CoursePost.php** - Posts within courses
- **CoursePostComment.php** - Comments on course posts
- **CoursePostCommentDislike.php** - Dislikes on course post comments
- **CoursePostCommentImage.php** - Images in course post comments
- **CoursePostCommentLike.php** - Likes on course post comments
- **CoursePostDislike.php** - Dislikes on course posts
- **CoursePostImage.php** - Images in course posts
- **CoursePostImageComment.php** - Comments on course post images
- **CoursePostImageCommentDislike.php** - Dislikes on course post image comments
- **CoursePostImageCommentLike.php** - Likes on course post image comments
- **CoursePostImageDislike.php** - Dislikes on course post images
- **CoursePostImageLike.php** - Likes on course post images
- **CoursePostLike.php** - Likes on course posts
- **CoursePostShare.php** - Shared course posts
- **CourseQuiz.php** - Course quizzes and tests (updated namespace)
- **CourseQuizResult.php** - Quiz results and scores
- **CourseSetting.php** - Course configuration settings
- **GuardianContact.php** - Guardian contact information
- **HomeVisitImage.php** - Images from home visits
- **HomeVisitParticipant.php** - Participants in home visits
- **JsmStudentInfo.php** - Student information from JSM system
- **Lesson.php** - Individual lessons within courses (updated namespace)
- **LessonComment.php** - Comments on lessons
- **LessonCommentDislike.php** - Dislikes on lesson comments
- **LessonCommentImage.php** - Images in lesson comments
- **LessonCommentLike.php** - Likes on lesson comments
- **LessonDislike.php** - Dislikes on lessons
- **LessonImage.php** - Images for lessons
- **LessonLike.php** - Likes on lessons
- **MentalMath.php** - Mental math exercises
- **Question.php** - Quiz and assignment questions
- **QuestionImage.php** - Images for questions
- **QuestionOption.php** - Options for multiple choice questions
- **Student.php** - Student information and records
- **StudentAcademicInfo.php** - Student academic records
- **StudentAddress.php** - Student address information
- **StudentCard.php** - Student identification cards
- **StudentContact.php** - Student contact information
- **StudentDocument.php** - Student document storage
- **StudentGuardian.php** - Student guardian information
- **StudentHealthInfo.php** - Student health records
- **StudentHomeVisit.php** - Home visit records
- **Topic.php** - Discussion topics
- **TopicImage.php** - Images for topics

### Earn (Income Generation)
Models related to monetization, donations, and revenue generation:

- **Donate.php** - Donation management (updated namespace)
- **DonateRecipient.php** - Donation recipients

### Shared (Common Models)
Models used across multiple categories or core system functionality:

- **Membership.php** - General membership system
- **PlearndAdmin.php** - System administrators
- **Support.php** - Customer support system
- **SupportViewer.php** - Support viewing tracking
- **Team.php** - Team management
- **TeamInvitation.php** - Team invitations
- **TempUserModel.php** - Temporary user data
- **User.php** - Core user model (updated namespace)
- **UserAnswerQuestion.php** - User responses to questions (moved to Learn folder)
- **UserProfile.php** - User profile information
- **VisitCounter.php** - Visit tracking system
- **VisitorCounter.php** - Visitor tracking system

## Namespace Updates

All copied models have been updated with proper namespaces:

- Play models: `namespace App\Models\Play;`
- Learn models: `namespace App\Models\Learn;`
- Earn models: `namespace App\Models\Earn;`
- Shared models: `namespace App\Models\Shared;`

## Cross-References

Models have been updated to reference other models using their new namespaces with full paths (e.g., `\App\Models\Play\Post::class`) to avoid conflicts.

## Benefits of This Structure

1. **Clear Separation**: Models are organized by their primary function within the Play-Learn-Earn ecosystem
2. **Maintainability**: Easier to locate and modify models based on their purpose
3. **Scalability**: New models can be easily categorized into appropriate folders
4. **Team Collaboration**: Developers can focus on specific functional areas
5. **Code Organization**: Reduces cognitive load when working with related functionality

## Migration Notes

- Original models remain in `app/Models/` for backward compatibility
- New organized copies are in their respective subfolders
- All namespace references have been updated in the copied files
- Cross-model relationships use fully qualified class names to avoid conflicts
- Academy and all related models are now properly categorized under Learn as requested