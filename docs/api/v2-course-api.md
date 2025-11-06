# Course API V2 Documentation

## Overview

The Course API V2 provides enhanced endpoints for managing courses, groups, and members with improved filtering, pagination, and data structure. All endpoints are prefixed with `/api/v2/` to ensure clear separation from v1 endpoints.

## Authentication

All API endpoints require authentication using Sanctum tokens. Include the token in the Authorization header:

```
Authorization: Bearer {your-token}
```

## Base URL

```
https://your-domain.com/api/v2
```

## Endpoints

### Courses

#### GET /courses

Get a paginated list of courses with filtering and search capabilities.

**Query Parameters:**
- `category` (string, optional): Filter by course category
- `level` (string, optional): Filter by course level
- `status` (string, optional): Filter by course status
- `search` (string, optional): Search in course name and description
- `per_page` (integer, optional): Number of items per page (default: 20)
- `page` (integer, optional): Page number

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Laravel Development",
      "slug": "laravel-development",
      "code": "LAR-101",
      "description": "Learn Laravel from scratch",
      "category": "programming",
      "level": "intermediate",
      "status": "active",
      "enrolled_students": 25,
      "saleable": true,
      "price": 99.99,
      "created_at": "2023-01-01T00:00:00.000000Z",
      "updated_at": "2023-01-01T00:00:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 100
  }
}
```

#### GET /courses/{id}

Get detailed information about a specific course.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Laravel Development",
    "slug": "laravel-development",
    "code": "LAR-101",
    "description": "Learn Laravel from scratch",
    "category": "programming",
    "level": "intermediate",
    "status": "active",
    "enrolled_students": 25,
    "settings": {
      "auto_accept_members": true
    },
    "groups": [...],
    "members": [...],
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z"
  },
  "stats": {
    "total_groups": 3,
    "total_members": 25,
    "active_members": 23,
    "completion_rate": 78.5
  }
}
```

#### PUT /courses/{id}

Update course information.

**Request Body:**
```json
{
  "name": "Updated Course Name",
  "description": "Updated description",
  "category": "advanced-programming",
  "level": "advanced",
  "status": "active",
  "auto_accept_members": true
}
```

#### GET /courses/{id}/summary

Get course summary statistics.

**Response:**
```json
{
  "success": true,
  "data": {
    "basic_info": {
      "id": 1,
      "name": "Laravel Development",
      "code": "LAR-101",
      "category": "programming",
      "level": "intermediate",
      "status": "active",
      "enrolled_students": 25
    },
    "statistics": {
      "total_groups": 3,
      "total_members": 25,
      "active_members": 23,
      "total_lessons": 12,
      "total_assignments": 8,
      "total_quizzes": 5
    },
    "progress": {
      "average_completion": 78.5,
      "average_grade": 3.2
    }
  }
}
```

### Course Groups

#### GET /courses/{courseId}/groups

Get groups for a specific course.

**Query Parameters:**
- `status` (boolean, optional): Filter by group status
- `search` (string, optional): Search in group name and description
- `per_page` (integer, optional): Number of items per page (default: 20)
- `page` (integer, optional): Page number

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "course_id": 1,
      "name": "Group A",
      "description": "Morning session group",
      "status": true,
      "members_count": 10,
      "active_members_count": 9,
      "created_at": "2023-01-01T00:00:00.000000Z",
      "updated_at": "2023-01-01T00:00:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 20,
    "total": 35
  }
}
```

#### POST /courses/{courseId}/groups

Create a new group in a course.

**Request Body:**
```json
{
  "name": "New Group",
  "description": "Group description",
  "status": true
}
```

#### PUT /groups/{groupId}

Update group information.

**Request Body:**
```json
{
  "name": "Updated Group Name",
  "description": "Updated description",
  "status": true
}
```

#### GET /groups/{groupId}/members

Get members of a specific group.

**Query Parameters:**
- `status` (boolean, optional): Filter by member status
- `search` (string, optional): Search in member name or email
- `per_page` (integer, optional): Number of items per page (default: 20)
- `page` (integer, optional): Page number

### Course Members

#### GET /courses/{courseId}/members

Get members of a specific course.

**Query Parameters:**
- `status` (boolean, optional): Filter by member status
- `role` (integer, optional): Filter by member role (1=student, 2=assistant, 3=instructor)
- `group_id` (integer, optional): Filter by group ID
- `search` (string, optional): Search in member name or email
- `sort_by` (string, optional): Sort field (default: order_number)
- `sort_order` (string, optional): Sort order (asc/desc, default: asc)
- `per_page` (integer, optional): Number of items per page (default: 20)
- `page` (integer, optional): Page number

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "course_id": 1,
      "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
      },
      "member_name": "John Doe",
      "role": 1,
      "status": 1,
      "grade_progress": "3.5",
      "achieved_score": 850,
      "bonus_points": 50,
      "completion_percentage": 85.5,
      "last_activity": "2023-01-01T12:00:00.000000Z",
      "created_at": "2023-01-01T00:00:00.000000Z",
      "updated_at": "2023-01-01T00:00:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 20,
    "total": 55
  }
}
```

#### PUT /members/{memberId}

Update member information.

**Request Body:**
```json
{
  "member_name": "Updated Name",
  "order_number": 5,
  "member_code": "MEM-001",
  "role": 2,
  "status": true,
  "group_id": 2,
  "bonus_points": 25,
  "grade_progress": "3.8",
  "notes_comments": "Additional notes"
}
```

## Error Responses

All endpoints return consistent error responses:

```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": [
      "Error message for this field"
    ]
  }
}
```

## HTTP Status Codes

- `200` - Success
- `201` - Created (for POST requests)
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

## Rate Limiting

API endpoints are rate-limited to prevent abuse. Current limits:
- 100 requests per minute per authenticated user
- 1000 requests per hour per authenticated user

Rate limit headers are included in responses:
- `X-RateLimit-Limit` - Request limit
- `X-RateLimit-Remaining` - Remaining requests
- `X-RateLimit-Reset` - Reset time (Unix timestamp)

## Versioning

The API version is specified in the URL path (`/api/v2/`). Future versions will be available at `/api/v3/`, etc.

## Backward Compatibility

V1 endpoints remain available at `/api/v1/` and will continue to be supported. No breaking changes will be made to V1 endpoints.

## Testing

Use the provided test suite to verify API functionality:

```bash
php artisan test tests/Feature/CourseV2ApiTest.php