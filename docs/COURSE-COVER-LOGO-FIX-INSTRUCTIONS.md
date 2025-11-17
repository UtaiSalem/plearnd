# Course Cover and Logo Image Upload Fix

## Problem
The course cover and logo image upload functionality was failing with 404 errors because the backend routes and controller methods were missing.

## Solution Implemented

### 1. Added Missing Routes
Added the following routes to `routes/learn/course.php`:
- `POST /courses/{course}/cover` - Update course cover image
- `POST /courses/{course}/logo` - Update course logo image
- `PATCH /courses/{course}/header` - Update course header text
- `PATCH /courses/{course}/subheader` - Update course subheader text
- `GET /courses/{course}/profile` - Get course profile data

### 2. Added Controller Methods
Added the following methods to `app/Http/Controllers/CourseController.php`:
- `updateCover()` - Handles cover image upload
- `updateLogo()` - Handles logo image upload
- `updateHeader()` - Updates course header text
- `updateSubheader()` - Updates course subheader text
- `profile()` - Returns course profile data

### 3. Created Database Migration
Created migration file `2025_11_14_100000_add_logo_and_headers_to_courses_table.php` to add:
- `logo` field - Store logo image filename
- `cover_header` field - Store header text
- `cover_subheader` field - Store subheader text

## Manual Steps Required

### 1. Run Database Migration
```bash
php artisan migrate
```

### 2. Create Logo Directory
```bash
mkdir -p storage/app/public/images/courses/logos
```

### 3. Ensure Storage Link is Created
```bash
php artisan storage:link
```

## How It Works

### Cover Image Upload
1. User selects a cover image in the CourseProfileCover component
2. JavaScript calls `courseProfileStore.updateCourseCover(courseId, file)`
3. Store makes POST request to `/courses/{courseId}/cover`
4. Controller validates the image, deletes old cover, uploads new one
5. Returns success response with new image URL
6. Component updates the UI with new image

### Logo Image Upload
1. User selects a logo image in the CourseProfileCover component
2. JavaScript calls `courseProfileStore.updateCourseLogo(courseId, file)`
3. Store makes POST request to `/courses/{courseId}/logo`
4. Controller validates the image, deletes old logo, uploads new one
5. Returns success response with new image URL
6. Component updates the UI with new image

## Error Handling
- Unauthorized users (non-course admins) will receive 403 error
- Invalid file types will be rejected with validation error
- File size limit is 4MB
- Old images are properly deleted before uploading new ones
- Proper error responses are returned to the frontend

## File Storage
- Cover images are stored in: `storage/app/public/images/courses/covers/`
- Logo images are stored in: `storage/app/public/images/courses/logos/`
- Public URLs are accessible via: `/storage/images/courses/covers/` and `/storage/images/courses/logos/`

## Testing
After completing the manual steps above, test the functionality by:
1. Navigate to a course page where you are the admin
2. Click on the camera icon for cover or logo
3. Select an image file (jpg, jpeg, png, gif, svg)
4. Verify the image uploads and displays correctly
5. Check browser network tab for successful API calls