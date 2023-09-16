<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function store()
    {
        
    }

    public function destroy(Assignment $assignment)
    {
        // $answers = $assignment->answers;
        foreach ( $assignment->answers as $answer) {            
            foreach ($answer->images as $image) {
                Storage::disk('public')->delete($image->image_url);
            }
            $answer->images()->delete();
        }

        foreach ($assignment->images as $image) {
            Storage::disk('public')->delete($image->image_url);
        }

        // $answers->delete();
        $assignment->answers()->delete();
        $assignment->images()->delete();
        $assignment->delete();

        return response()->json(['success' => true], 200);
    }
}
