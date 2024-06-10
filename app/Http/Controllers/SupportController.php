<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Support;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Storage;

class SupportController extends Controller
{
    public function index()
    {
        return Inertia::render('Support');
    }

    public function donate(){
        return Inertia::render('Support/SupportDonate');
    }
    public function storeDonate(Request $request){
        // dd($request->all());
        return response()->json([
            'success' => true,
            'message' => 'success'
        ]);
    }

    public function loan()
    {
        return Inertia::render('Support/SupportLoan');
    }
    public function advertise()
    {
        return Inertia::render('Support/SupportAndAdvert',[
            'Supporter' => new UserResource(auth()->user()),
        ]);
    }

    public function storeAdvertise(Request $request)
    {
        $validated = $request->validate([
            'supporter' => '',
            'amounts' => 'required',
            'duration' => 'required',
            'total_views' => 'required',
            'transfer_date' => 'required',
            'transfer_time' => 'required',
            'slip' => 'required|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
            'media_image' => 'required|image|mimes:jpg,jpeg,png,gif,svg|max:4096',
        ]);

        try {
            if($request->file('slip')) {
                $slip_file = $request->file('slip');
                $slip_filename =  uniqid().'.'.$slip_file->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('images/supports/slips', $slip_file, $slip_filename);
            }
            if($request->file('media_image')) {
                $media_file = $request->file('media_image');
                $media_filename =  uniqid().'.'.$media_file->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('images/supports/medias', $media_file, $media_filename);
            }

            $newSupport = new Support();
            $newSupport->user_id = auth()->id();
            $newSupport->supporter = $validated['supporter'];
            $newSupport->amounts = $validated['amounts'];
            $newSupport->duration = $validated['duration'];
            $newSupport->total_views = $validated['total_views'];
            $newSupport->remaining_views = $validated['total_views'];
            $newSupport->transfer_date = $validated['transfer_date'];
            $newSupport->transfer_time = $validated['transfer_time'];
            $newSupport->slip = $slip_filename;
            $newSupport->media_image = $media_filename;
            $newSupport->save();

            return to_route('/');
        } catch (\Throwable $th) {
            throw $th;
        }

    }

    public function storeLoan(Request $request)
    {
        $validated = $request->validate([
            'supporter' => '',
            'amounts' => 'required',
            'duration' => 'required',
            'total_views' => 'required',
            'transfer_date' => 'required',
            'transfer_time' => 'required',
            'slip' => 'required|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
            'media_image' => 'required|image|mimes:jpg,jpeg,png,gif,svg|max:4096',
        ]);

        try {
            if($request->file('slip')) {
                $slip_file = $request->file('slip');
                $slip_filename =  uniqid().'.'.$slip_file->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('images/supports/slips', $slip_file, $slip_filename);
            }
            if($request->file('media_image')) {
                $media_file = $request->file('media_image');
                $media_filename =  uniqid().'.'.$media_file->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('images/supports/medias', $media_file, $media_filename);
            }

            $newSupport = new Support();
            $newSupport->user_id = auth()->id();
            $newSupport->supporter = $validated['supporter'];
            $newSupport->amounts = $validated['amounts'];
            $newSupport->duration = $validated['duration'];
            $newSupport->total_views = $validated['total_views'];
            $newSupport->remaining_views = $validated['total_views'];
            $newSupport->transfer_date = $validated['transfer_date'];
            $newSupport->transfer_time = $validated['transfer_time'];
            $newSupport->slip = $slip_filename;
            $newSupport->media_image = $media_filename;
            $newSupport->save();

            return to_route('/');
        } catch (\Throwable $th) {
            throw $th;
        }

    }


}
