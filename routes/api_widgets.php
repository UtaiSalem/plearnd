<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CourseAssignmentApiController;

// Course Assignments API
Route::get('/courses/{course}/assignments/upcoming', [CourseAssignmentApiController::class, 'upcoming']);
