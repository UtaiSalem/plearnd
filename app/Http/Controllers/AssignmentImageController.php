<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssignmentImage;
use Illuminate\Support\Facades\Storage;

class AssignmentImageController extends Controller
{
    public function destroy(AssignmentImage $asmimage)
    {
        // Storage::disk('public')->delete($asmimage->image_url);
        Storage::disk('public')->delete('images/courses/assignments/'.$image->image_url);
        $asmimage->delete();

        return response()->json(['success' => true ], 200);
    }
}
