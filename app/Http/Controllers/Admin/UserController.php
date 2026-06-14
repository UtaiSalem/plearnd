<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $query = User::query()->withCount('bookings')->with('managedRooms');

        // Apply filters
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $search = '%' . $request->q . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('email', 'like', $search)
                  ->orWhere('username', 'like', $search)
                  ->orWhere('student_id', 'like', $search)
                  ->orWhere('employee_id', 'like', $search);
            });
        }

        $users = $query->orderBy('role')->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['status'] = 'active';

        User::create($data);

        return redirect()->route('admin.users.index')->with('status', 'สร้างผู้ใช้งานใหม่เรียบร้อยแล้ว');
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        
        $user->update($data);

        return redirect()->route('admin.users.index')->with('status', 'อัปเดตข้อมูลผู้ใช้งานเรียบร้อยแล้ว');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        // Soft block check: prevent deleting if they have active bookings or are managing rooms
        $activeBookingsCount = $user->bookings()->whereNotIn('status', ['rejected'])->whereNull('cancelled_at')->count();
        $managingRoomsCount = $user->managedRooms()->count();

        if ($activeBookingsCount > 0 || $managingRoomsCount > 0) {
            return back()->withErrors(['error' => 'ไม่สามารถลบผู้ใช้งานได้ เนื่องจากยังมีรายการจองที่ดำเนินการอยู่ หรือยังเป็นผู้ดูแลห้องปฏิบัติการ']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'ลบผู้ใช้งานเรียบร้อยแล้ว');
    }

    public function changeStatus(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $data = $request->validate([
            'status' => ['required', 'in:active,inactive,suspended'],
        ]);

        if ($user->id === $request->user()->id && $data['status'] !== 'active') {
            return back()->withErrors(['error' => 'คุณไม่สามารถระงับบัญชีของตนเองได้']);
        }

        $user->update(['status' => $data['status']]);

        return back()->with('status', 'เปลี่ยนสถานะผู้ใช้งานเรียบร้อยแล้ว');
    }
}
