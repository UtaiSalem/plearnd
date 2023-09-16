<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Academy;
use Illuminate\Http\Request;
use App\Http\Resources\CourseResource;
use App\Http\Resources\AcademyResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
// use App\Http\Requests\StoreAcademyRequest;
// use App\Http\Requests\UpdateAcademyRequest;

class AcademyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        // $validated = $request->validate([
        //     'name' => 'required',
        //     'slogan' => 'required',
        //     'address'=> '',
        //     'cover'=> '',
        //     'logo'=> '',
        // ]);

        
        // $validated = $request->validate([
        // $validated = Validator::make($request->all(), [
        //     'name' => 'required|string|max:255',
        //     'slogan' => 'required|string|max:255',
        //     'address' => 'required|string',
        //     'logo' => 'nullable|image|max:2048',
        //     'cover' => 'nullable|image|max:2048',
        // ]);

        // dump($validated);
                
        // if ($validated->fails()) {
        //     return response()->json(['error' => $validated->errors()], 422);
        // }
        
        // return response()->json([
        //     'request' => [
        //         'name' => $request->input('name'),
        //         'slogan' => $request->input('slogan'),
        //         'address' => $request->input('address'),
        //         'logo' => $request->input('logo'),
        //         'cover' => $request->input('cover'),
        //     ],   
        // ]);

        // $request->validate([
        //         'logo' => 'required|mimes:jpg,jpeg,png,gif|max:10240'    
        // ]);

        // if ($request->hasFile('logo')) {
        //     return response()->json([
        //         'hasLogo' => true,
        //         'logo' => $request->all(),
        //     ]);
        // }else{
        //     return response()->json([
        //         'hasLogo' => false,
        //         'logo' => $request->all(),
        //     ]);
        // }
        // if ($request->hasFile('cover')) {
        //     $cover = $request->file('cover');
        //     $coverPath = $cover->store('covers', 'public/storage/assets/images');
        //     // $academy->cover = $coverPath;
        // }

        // try {
        //     $user = auth()->user();
        //     $academy = $user->academies()->create([
        //         'name' => $validated['name'],
        //         'slogan' => $validated['slogan'],
        //         'address' => $validated['address'],
        //         'logo' => $logoPath,
        //         'cover' => $coverPath,
        //     ]);
        // } catch (\Throwable $th) {
        //     throw $th;
        // }

        // dd($academy);

        // return to_route('academy.show', $academy->id);

        if($request->file()) {
            $file_name = time().'_'.$request->file->getClientOriginalName();
            // $file_path = $request->file('logo')->storeAs('logos', $file_name, 'public');

            // $fileUpload->name = time().'_'.$request->file->getClientOriginalName();
            // $fileUpload->logo = '/storage/' . $file_path;
            // $fileUpload->user_id = auth()->id();
            // $fileUpload->save();

            // return response()->json([
            //     'success'=>'File uploaded successfully.'
            // ]);
        }
        return response()->json([
            'filePath' => $file_name
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Academy $academy)
    {
        if ($academy->user_id == auth()->id()) {
            $isAcademyAdmin = true;
        }else{
            $isAcademyAdmin = false;
        }
        return Inertia::render('Academy', [ 
            'academy'   => new AcademyResource($academy),
            'courses'   => CourseResource::collection($academy->courses),
            'imagePath' => '/../../'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Academy $academy)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Academy $academy, Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'slogan' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'cover' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048',
            'logo' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if($request->hasFile('cover')) {
            if($academy->cover && ($academy->cover !== 'covers/default_cover.png')){
                Storage::disk('public')->delete($academy->cover);
            };
            
            $cover_name = time().'_'.$request->cover->getClientOriginalName();
            $cover_path = $request->file('cover')->storeAs('covers', $cover_name, 'public');
            $academy->cover = $cover_path;
        }

        if($request->hasFile('logo')) {
            if($academy->logo && ($academy->logo !== 'logos/default_logo.png')){
                Storage::disk('public')->delete($academy->logo);
            };

            $logo_name = time().'_'.$request->logo->getClientOriginalName();
            $logo_path = $request->file('logo')->storeAs('logos', $logo_name, 'public');
            $academy->logo = $logo_path;
        }

        if($request->name){ $academy->name = $request->name; }
        if($request->slogan){  $academy->slogan = $request->slogan; }
        if($request->address){  $academy->address = $request->address; }

        $academy->update();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Academy $academy)
    {
        //
    }

    public function getAuthAcademies(User $user){
        // return $user->academies();
        // $userAcademies = Academy::where('user_id', $user->id)->get();
        return response()->json([
            'academies' =>  $user->academies()->oldest()->get(['id','name']),
        ], 200);
    }

        /**
     * Store a newly created academy in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function temp_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'director' => 'nullable|string|max:255',
            'established_year' => 'nullable|integer|min:1900',
            'type' => 'nullable|string|max:255',
            'accreditation' => 'nullable|string|max:255',
            'accreditation_body' => 'nullable|string|max:255',
            'total_students' => 'nullable|integer|min:0',
            'total_teachers' => 'nullable|integer|min:0',
            'courses_offered' => 'nullable|string',
            'facilities' => 'nullable|string',
            'academy_timings' => 'nullable|string',
            'holidays' => 'nullable|string',
            'social_media_links' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Create and save the academy
        $academy = new Academy();
        $academy->name = $request->input('name');
        $academy->address = $request->input('address');
        $academy->email = $request->input('email');
        $academy->phone = $request->input('phone');
        $academy->director = $request->input('director');
        $academy->established_year = $request->input('established_year');
        $academy->type = $request->input('type');
        $academy->accreditation = $request->input('accreditation');
        $academy->accreditation_body = $request->input('accreditation_body');
        $academy->total_students = $request->input('total_students', 0);
        $academy->total_teachers = $request->input('total_teachers', 0);
        $academy->courses_offered = $request->input('courses_offered');
        $academy->facilities = $request->input('facilities');
        $academy->academy_timings = $request->input('academy_timings');
        $academy->holidays = $request->input('holidays');
        $academy->social_media_links = $request->input('social_media_links');
        
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoPath = $logo->store('logos', 'public');
            $academy->logo = $logoPath;
        }

        $academy->save();

        return response()->json($academy, 201);
    }

    public function upload(Request $request)
    {           
        $request->validate([
            'name' => 'required|string|max:255',
            'slogan' => 'required|string|max:255',
            'address' => 'nullable|string',
            'cover' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048',
            'logo' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $academy = new Academy();

        if($request->file('cover')) {
            $cover_name = time().'_'.$request->cover->getClientOriginalName();
            $cover_path = $request->file('cover')->storeAs('covers', $cover_name, 'public');
            $academy->cover = $cover_path;
        }else{
            $academy->cover = 'covers/default_cover.png';
        }

        if($request->file('logo')) {
            $logo_name = time().'_'.$request->logo->getClientOriginalName();
            $logo_path = $request->file('logo')->storeAs('logos', $logo_name, 'public');
            $academy->logo = $logo_path;
        }else{
            $academy->logo = 'logos/default_logo.png';
        }

        $academy->user_id = auth()->id();
        $academy->name = $request->name;
        $academy->slogan = $request->slogan;
        $academy->address = $request->address;

        $academy->save();

        return response()->json([
            'success' => true,
            'academy' => Academy::where('id', $academy->id)->select('id', 'name')->get(),
        ]);
   }
}
