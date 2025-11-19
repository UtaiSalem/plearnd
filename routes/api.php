<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Learn\Student\HomeVisit\TeacherController;
use App\Http\Controllers\Learn\Student\HomeVisit\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Home Visit API Routes
Route::prefix('home-visit')->group(function () {
    Route::post('/{homeVisit}/update', [TeacherController::class, 'updateHomeVisitWithImages'])->name('api.home-visit.update');
    Route::post('/image/{imageId}/update-description', function(Request $request, $imageId) {
        // TODO: Implement image description update
        return response()->json(['success' => true, 'message' => 'Image description updated']);
    })->name('api.home-visit.image.update-description');
    
    // Admin Reports API
    Route::prefix('admin')->group(function () {
        Route::get('/visits', [AdminController::class, 'getAllVisits'])->name('api.home-visit.admin.visits');
        Route::get('/visits/{visit}/report', [AdminController::class, 'downloadReport'])->name('api.home-visit.admin.report');
        Route::post('/visits/export/excel', [AdminController::class, 'exportToExcel'])->name('api.home-visit.admin.export.excel');
        Route::post('/visits/export/pdf', [AdminController::class, 'exportToPDF'])->name('api.home-visit.admin.export.pdf');
    });
});

// Course V2 API Routes
// require __DIR__.'/learn/courseV2.php';
