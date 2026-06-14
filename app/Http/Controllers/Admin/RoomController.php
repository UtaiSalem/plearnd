<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(?Room $edit = null): View
    {
        $rooms = Room::with('manager')->orderBy('name')->get();
        $managers = User::whereIn('role', ['staff', 'admin'])->orderBy('name')->get();
        return view('admin.rooms.index', compact('rooms', 'edit', 'managers'));
    }

    public function edit(Room $room): View
    {
        return $this->index($room);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->save($request);
        return redirect()->route('admin.rooms.index')->with('status', 'บันทึกข้อมูลห้องเรียบร้อย');
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $this->save($request, $room);
        return redirect()->route('admin.rooms.index')->with('status', 'อัปเดตข้อมูลห้องเรียบร้อย');
    }

    private function save(Request $request, ?Room $room = null): void
    {
        $codeRule = ['required', 'string', 'max:20'];
        $codeRule[] = $room
            ? Rule::unique('rooms', 'code')->ignore($room->id)
            : Rule::unique('rooms', 'code');

        $data = $request->validate([
            'code' => $codeRule,
            'name' => ['required', 'string', 'max:120'],
            'building' => ['required', 'string', 'max:120'],
            'floor' => ['required', 'string', 'max:20'],
            'category' => ['required', 'string', 'max:80'],
            'capacity' => ['required', 'integer', 'min:1'],
            'manager_user_id' => ['nullable', 'exists:users,id'],
            'manager_name' => ['nullable', 'string', 'max:120'],
            'contact' => ['nullable', 'string', 'max:60'],
            'status' => ['required', Rule::in(['available', 'limited', 'maintenance'])],
            'open_hours' => ['required', 'string', 'max:60'],
            'summary' => ['required', 'string', 'max:500'],
            'equipment' => ['nullable', 'string', 'max:500'],
        ]);

        $room ? $room->update($data) : Room::create($data);
    }
}
