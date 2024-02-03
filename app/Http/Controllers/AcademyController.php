<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Academy;
use Illuminate\Http\Request;
use App\Models\AcademyMember;
use Intervention\Image\Facades\Image;
use App\Http\Resources\CourseResource;
use App\Http\Resources\AcademyResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AcademyController;
use App\Http\Resources\AcademyMemberResource;

class AcademyController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Learn/Academy/Academies', [
            'authMemberAcademies'   => AcademyResource::collection(auth()->user()->academyMembers),
            'authOwnerAcademies'    => AcademyResource::collection(auth()->user()->academies()->paginate(10)),
            'allAcademies'          => AcademyResource::collection(Academy::paginate(10)),
            
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Learn/Academy/CreateNewAcademy');
    }
    public function create_course(Academy $academy)
    {
        $isAcademyAdmin = $academy->user_id == auth()->id();
        
        return Inertia::render('Learn/Academy/CreateNewAcademyCourse', [ 
            'academy'           => new AcademyResource($academy),
            'courses'           => CourseResource::collection($academy->courses()->paginate()),
            'isAcademyAdmin'    => $isAcademyAdmin,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        if (auth()->user()->pp < 1000000) {
            return response()->json([
                'success' => false,
                'message' => 'แต้มสะสมไม่เพียงพอ, กรุณาเพิ่มแต้มสะสม'
            ], 200);
        }
        
        $validated = $request->validate([
            'name'              => 'required|string',
            'slogan'            => 'required|string',
            'address'           => 'required|string',
            'autoAcceptMember'  => 'required|string',
            'membershipFees'    => 'required|integer',
            'logo'              => 'image|mimes:jpg,jpeg,png,gif,svg',
            'cover'             => 'image|mimes:jpg,jpeg,png,gif,svg',
        ]);

        // return $validated['autoAcceptMember'] === true ? true: false;

        try {
            $authUser = auth()->user();

            if($request->hasFile('logo')) {
                // $logo = $request->file('logo');
                $logo = $validated['logo'];
                $logo_name = uniqid() . '.' . $logo->getClientOriginalExtension();

                // $logo_image = Image::make($logo->getRealPath());
                // $logo_image->resize(300, 300, function ($constraint) {
                //     $constraint->aspectRatio();
                // });

                $logo_url = Storage::disk('public')->putFileAs('images/academies/logos', $logo, $logo_name);
            }

            if($request->hasFile('cover')) {
                $cover = $validated['cover'];
                $cover_name = uniqid() . '.' . $cover->getClientOriginalExtension();

                $cover_url = Storage::disk('public')->putFileAs('images/academies/covers', $cover, $cover_name);
            }

            // $newAcademy = $authUser->academies()->create([
            //     'name' => $validated['name'],
            //     'slogan' => $validated['slogan'],
            //     'address' => $validated['address'],
            //     'director' => auth()->id(),
            //     'logo'  => $logo_name ?? null,
            //     'cover'  => $cover_name ?? null,
            // ]);

            $academy = new Academy();
            $academy->user_id = auth()->id();
            $academy->name = $validated['name'];
            $academy->slogan = $validated['slogan'];
            $academy->address = $validated['address'];
            $academy->membership_fees_points = $validated['membershipFees'];
            $academy->director = auth()->id();
            $academy->logo = $logo_name ?? null;
            $academy->cover = $cover_name ?? null;

            $academy->save();
            
            // $academy->academySetting->auto_accept_members = $validated['autoAcceptMember'];
            $academy->academySetting()->create([
                'auto_accept_members' => $validated['autoAcceptMember'] === 'true' ? true: false,
            ]);

            auth()->user()->decrement('pp', 860000);

            // return redirect('/academies/'.$academy->id);
            return response()->json([
                'success' => true,
                'academy' => $academy->id
            ], 200);

        } catch (\Throwable $th) {
            return throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Academy $academy)
    {
        $isAcademyAdmin = $academy->user_id == auth()->id();

        return Inertia::render('Learn/Academy/ShowAcademy',[
            'academy'           => new AcademyResource($academy),
            'courses'           => CourseResource::collection($academy->courses()->paginate()),
            'isAcademyAdmin'    => $isAcademyAdmin,
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
            if($academy->cover && ($academy->cover !== 'default_cover.png')){
                Storage::disk('public')->delete($academy->cover);
            };

            $cover = $validated['cover'];
            
            $cover_name = uniqid() . '.' . $cover->getClientOriginalExtension();
            $cover_path = Storage::disk('public')->putFileAs('images/academies/covers', $cover, $cover_name);
            $academy->cover = $cover_name;
        }

        if($request->hasFile('logo')) {
            if($academy->logo && ($academy->logo !== 'default_logo.png')){
                Storage::disk('public')->delete($academy->logo);
            };

            $logo = $validated['logo'];

            $logo_name = uniqid() . '.' . $logo->getClientOriginalExtension();
            $logo_path = Storage::disk('public')->putFileAs('images/academies/logos', $logo, $logo_name);
            $academy->logo = $logo_name;
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
}
