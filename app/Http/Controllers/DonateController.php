<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Donate;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\DonateResource;
use App\Http\Resources\ActivityResource;
// use App\Filament\PlearndAdmin\Resources\DonateResource;

class DonateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('PlearndAdmin/Donation/DonationList', [
            'donates' => DonateResource::collection(Donate::latest()->paginate()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $donor = Auth::user();

        return Inertia::render('Support/Donate/CreateDonate', [
            'donor' => $donor,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       
        $validated = $request->validate([
            'user_id'      => ['nullable'],
            'donor_id'      => ['nullable'],
            'donor_name'    => ['nullable'],
            'amounts'       => ['required', 'numeric'],
            'transfer_date' => ['required'],
            'transfer_time' => ['required'],
        ]);
       
        if($request->file('slip')) {
            $slip_file = $request->file('slip');
            $slip_filename =  uniqid().'.'.$slip_file->getClientOriginalExtension();
            $slip_file->storeAs('public/images/supports/donates/slips', $slip_filename);
        }

        $donate = new Donate();

        $donate->user_id            = $validated['user_id'] ?? 7;
        $donate->donor_id           = !$validated['donor_id'] || $validated['donor_id'] === 'null' ? null : $validated['donor_id'];
        $donate->donor_name         = $validated['donor_name'] ?? null;
        
        $donate->amounts            = $validated['amounts'];
        $donate->slip               = $slip_filename;
        $donate->transfer_date      = Carbon::parse($request->transfer_date);
        $donate->transfer_time      = $validated['transfer_time'];
        $donate->remaining_points   = $validated['amounts']*1080;
        $donate->save();

        $activity = new Activity();
        $activity->user_id = $donate->donor_id ?? 7;
        $activity->activity_type = 'givedonation';
        $activity->activityable()->associate($donate);
        $activity->save();

        return response()->json([
            'success' => true,
            'donate' => $donate,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Donate $donate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Donate $donate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Donate $donate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Donate $donate)
    {
        //
    }

    // get donor
    public function getDonor(User $user)
    {
        return response()->json([
            'success' => true,
            'donor' => $user,
        ]);
    }

    //recieve
    public function recieve(Donate $donate)
    {
        $donate->update([
            'status'        => 1,
            'approved_by'   => auth()->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'donate' => $donate,
        ], 200);
    }

    //cancel
    public function reject(Donate $donate)
    {
        $donate->update([
            'status' => 2,
            'approved_by'   => auth()->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'donate' => $donate,
        ], 200);
    }

    //get donate
    public function getDonate(Donate $donate)
    {
        try {
            if($donate->remaining_points >= 270){
                $authUser = auth()->user();

                $donate->recipients()->attach($authUser->id);

                
                $donate->decrement('remaining_points', 270);
                
                $authUser->increment('pp', 240);
                 
                $suggesterCode = $authUser->suggester_code ?? 33333333;
                
                $suggester = User::where('personal_code', $authUser->suggester_code)->first();

                if($suggester) {
                    $suggester->increment('pp', 30);
                }
                
                $activity = new Activity();
                $activity->user_id = $authUser->id;
                $activity->activity_type = 'recieveddonation';
                $activity->activityable()->associate($donate->donateRecipients()->where('user_id', $authUser->id)->latest()->first());
                $activity->save();
       

                $donate->refresh();
        
                return response()->json([
                    'success' => true,
                    'donate' => $donate,
                    'activity' => new ActivityResource($activity),
                ]);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'การสนับสนุนนี้หมดแล้ว',
                ]);
            }
        } catch (\Throwable $th) {
            // throw $th;
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถสนับสนุนได้',
                'error' => $th->getMessage(),
            ]);
        }
    }
}