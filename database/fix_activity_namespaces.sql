-- SQL script to fix activityable_type namespace values in activities table

-- Fix Post namespace
UPDATE activities 
SET activityable_type = 'App\Models\Play\Post' 
WHERE activityable_type = 'App\Models\Post' 
   OR activityable_type = 'Post';

-- Fix AcademyPost namespace
UPDATE activities 
SET activityable_type = 'App\Models\Learn\Academy\AcademyPost' 
WHERE activityable_type = 'App\Models\AcademyPost' 
   OR activityable_type = 'AcademyPost';

-- Fix CoursePost namespace
UPDATE activities 
SET activityable_type = 'App\Models\Learn\Course\Post\CoursePost' 
WHERE activityable_type = 'App\Models\CoursePost' 
   OR activityable_type = 'CoursePost';

-- Fix Donate namespace
UPDATE activities 
SET activityable_type = 'App\Models\Earn\Donate' 
WHERE activityable_type = 'App\Models\Donate' 
   OR activityable_type = 'Donate';

-- Fix Support namespace
UPDATE activities 
SET activityable_type = 'App\Models\Earn\Support' 
WHERE activityable_type = 'App\Models\Support' 
   OR activityable_type = 'Support';

-- Fix DonateRecipient namespace
UPDATE activities 
SET activityable_type = 'App\Models\Earn\DonateRecipient' 
WHERE activityable_type = 'App\Models\DonateRecipient' 
   OR activityable_type = 'DonateRecipient';

-- Fix Poll namespace
UPDATE activities 
SET activityable_type = 'App\Models\Play\Poll' 
WHERE activityable_type = 'App\Models\Poll' 
   OR activityable_type = 'Poll';

-- Check for any other potential namespace issues
SELECT DISTINCT activityable_type, COUNT(*) as count 
FROM activities 
GROUP BY activityable_type 
ORDER BY count DESC;