<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Friend;
use App\Http\Requests\StoreFriendRequest;
use App\Http\Requests\UpdateFriendRequest;

class FriendController extends Controller
{
    // global variable

    /**
     * Store a newly created resource in storage.
     */
    public function addFriendRequest(User $recipient)
    {
        $user = auth()->user();

        $user->befriend($recipient);

        return response()->json([
            'success' => true,
            'user' => $recipient,
            'message' => 'Friend request sent successfully.'
        ], 200);
    }

    /**
     * accept friend request
     */
    public function acceptFriendRequest(User $friend)
    {
        $user = auth()->user();

        $user->acceptFriendRequest($friend);

        return response()->json([
            'success' => true,
            'message' => 'Friend request accepted successfully.'
        ], 200);
    }

    /**
     * deny friend request
     */
    public function denyFriendRequest(User $friend){

        // $user = auth()->user();
        auth()->user()->denyFriendRequest($friend);

        return response()->json([
            'success' => true,
            'message' => 'Friend request denied successfully.'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $friend)
    {
        $user = auth()->user();

        $user->unfriend($friend);

        return response()->json([
            'success' => true,
            'message' => 'Friend removed successfully.'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteFriendRequest(User $friend)
    {
        $user = auth()->user();

        $user->unfriend($friend);

        return response()->json([
            'success' => true,
            'message' => 'Friend removed successfully.'
        ], 200);
    }
}
