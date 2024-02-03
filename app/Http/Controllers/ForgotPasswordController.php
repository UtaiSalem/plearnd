<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    public function index(){

        if (auth()->id() !== 1) {
            return redirect('/dashboard');
        }
        return Inertia::render('Admin/Resetpassword');
    }

    public function getUser(Request $request){
        $email = $request->email;

        $users = UserResource::collection(User::where('email','like','%'. $email . '%')
        ->orWhere('name','like','%'. $email . '%')
        ->limit(10)->get());

        return response()->json([
            'users' => $users,
            'success' => true,
        ], 200);

    }

    public function resetPassword(User $user)
    {
        if ($user->pp < 4800) {
            return redirect()->back()->with([
                'success' => false,
                'message' => 'แต้มสะสมไม่เพียงพอ'
            ]);
        }

        $user->update([
            'password' => Hash::make('0000'),
            'pp' => $user->pp - 4800,
        ]);

        DB::table('password_reset_tokens')->where(['email'=> $user->email])->delete();

        return response()->json([
            'success' => true,
            'message' => 'รีเซ็ตรหัสผ่านแล้ว'
        ], 200);

    }

    public function exchangeMoney(User $user, Request $request)
    {
        try {
            if ($request->money && $request->money < 0) {
                return redirect()->back()->with([
                    'success' => false,
                    'message' => 'จำนวนเงินน้อยเกินไป'
                ]);
            }

            $user->increment('pp', $request->money*1080);

            return response()->json([
                'success' => true,
                'message' => 'เพิ่มแต้มเส็จสมบูรณ์',
                'pp' => $user->pp,
            ], 200);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

}