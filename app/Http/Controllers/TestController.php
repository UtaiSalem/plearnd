<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Academy;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        return Inertia::render('Test');
    }

    public function upload(Request $request)
    {           
        $request->validate([
           'cover' => 'required|mimes:jpg,jpeg,png|max:2048'
        ]);

        // $fileUpload = new Academy();

        if($request->file('cover')) {
            $file = $request->file('cover');
            $fileName = 'profile-'.time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('files', $fileName);


            // $file_name = time().'_'.$request->cover->getClientOriginalName();
            // $file_path = $request->file('cover')->storeAs('logos', $file_name, 'public');


            // $fileUpload->name = time().'_'.$request->file->getClientOriginalName();
            // $fileUpload->logo = '/storage/' . $file_path;
            // $fileUpload->user_id = auth()->id();
            // $fileUpload->save();

            // return response()->json([
            //     'success'=>'File uploaded successfully.'
            // ]);
        }

        return response()->json([
            'success'=>'File uploaded successfully.',
            'cover' => $path
        ]);
   }
}
