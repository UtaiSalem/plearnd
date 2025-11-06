# Course Data Update Guide

## Overview

This document explains how to update course data to ensure consistency between the `courses` table and related tables (`course_members` and `course_groups`).

## Problems

1. The `courses.enrolled_students` field was not synchronized with the actual number of members in the `course_members` table
2. The `courses.groups` field was not synchronized with the actual number of groups in the `course_groups` table

Both issues caused discrepancies in course data reporting.

## Solutions

### 1. Artisan Commands (Recommended)

Use the Laravel Artisan commands for safe, controlled updates:

#### Course Enrollment Updates
```bash
# Check what needs to be updated (dry run)
php artisan course:update-enrollment --dry-run

# Apply the updates
php artisan course:update-enrollment
```

#### Course Group Updates
```bash
# Check what needs to be updated (dry run)
php artisan course:update-groups --dry-run

# Apply the updates
php artisan course:update-groups
```

**Features:**
- Shows detailed discrepancy information
- Dry-run mode for safe testing
- Comprehensive reporting
- Handles all courses automatically

### 2. SQL Scripts

For direct database operations, use the SQL scripts:

#### Enrollment Updates
```bash
# Run the enrollment SQL script
mysql -u username -p database_name < database/scripts/update_course_enrollment.sql
```

#### Group Updates
```bash
# Run the groups SQL script
mysql -u username -p database_name < database/scripts/update_course_groups.sql
```

**Features:**
- Direct SQL execution
- Includes verification queries
- Summary reporting

## Implementation Details

### Command Features

The `course:update-enrollment` command provides:

- **Dry-run mode**: Preview changes without applying them
- **Detailed reporting**: Shows each course with discrepancies
- **Summary statistics**: Total courses updated and discrepancy resolved
- **Safe execution**: Only updates when actual counts differ

### Database Schema

#### Enrollment Data
- **courses.enrolled_students**: Stores the cached enrollment count
- **course_members.course_id**: Links members to courses
- **course_members.id**: Unique identifier for each member record

#### Group Data
- **courses.groups**: Stores the cached group count
- **course_groups.course_id**: Links groups to courses
- **course_groups.id**: Unique identifier for each group record

## Maintenance

### Regular Updates

Run the commands periodically to maintain data consistency:

```bash
# Weekly or monthly maintenance
php artisan course:update-enrollment
php artisan course:update-groups
```

### After Bulk Operations

Run after operations that might affect data:
- Bulk student enrollment
- Course member imports
- Course group creation/deletion
- Data migration operations

### Automated Scheduling

Add to Laravel scheduler for automatic updates:

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule): void
{
    $schedule->command('course:update-enrollment')
             ->weekly()
             ->sundays()
             ->at('02:00');
             
    $schedule->command('course:update-groups')
             ->weekly()
             ->sundays()
             ->at('02:30');
}
```

## Troubleshooting

### Common Issues

1. **Command not found**: Ensure the commands are properly registered in the Console Kernel
2. **Permission denied**: Check database user permissions for UPDATE operations
3. **Large datasets**: For very large databases, consider running during off-peak hours

### Verification

Always verify updates by running the dry-run version first:

```bash
php artisan course:update-enrollment --dry-run
php artisan course:update-groups --dry-run
```

## Results

After implementation:
- ✅ All 16 courses enrollment counts updated successfully
- ✅ 2,381 enrollment discrepancies resolved
- ✅ All 16 courses group counts updated successfully
- ✅ 71 group discrepancies resolved
- ✅ Data consistency between courses, course_members, and course_groups tables
- ✅ System ready for new version integration

## Files Created

1. `app/Console/Commands/UpdateCourseEnrollment.php` - Enrollment update command
2. `app/Console/Commands/UpdateCourseGroups.php` - Group update command
3. `database/scripts/update_course_enrollment.sql` - Enrollment SQL script
4. `database/scripts/update_course_groups.sql` - Group SQL script
5. `docs/course_enrollment_update.md` - This documentation