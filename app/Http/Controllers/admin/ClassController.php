<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\User;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Classes::with('coach')->withCount('users');

        if ($search = $request->search) {
            $query->where('name', 'like', "%$search%")
                ->orWhereHas('coach', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
        }

        $classes = $query->latest()->paginate(10);

        return view('admin.class.index', compact('classes'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $coaches = User::where('role', 'pelatih')->get();
        return view('admin.class.create', compact('coaches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sanggar_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'coach_id' => 'required|exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['name', 'sanggar_name', 'description', 'coach_id']);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/classes', 'public');
            $data['image_path'] = $imagePath;
        }

        Classes::create($data);

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Classes $class)
    {
        $users = $class->users;
        $schedules = \App\Models\Schedule::where('class_id', $class->id)->get()->keyBy('id');
        $allUsers = User::where('role', 'user')->get();

        return view('admin.class.show', compact('class', 'users', 'allUsers', 'schedules'));
    }

    public function addUser(Request $request, Classes $class)
    {
        $request->validate([
            'register_type' => 'required|in:existing,new',
            'schedule_id' => 'required|exists:schedules,id',
            'is_paid_per_session' => 'sometimes|boolean',
        ]);

        if ($request->register_type === 'existing') {
            $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);
            $userId = $request->user_id;
        } else {
            $request->validate([
                'new_name' => 'required|string|max:255',
                'new_email' => 'required|email|unique:users,email',
            ]);
            $user = User::create([
                'name' => $request->new_name,
                'email' => $request->new_email,
                'role' => 'user',
                'password' => bcrypt('password123'),
            ]);
            $userId = $user->id;
        }

        $alreadyExists = $class->users()
            ->where('user_id', $userId)
            ->wherePivot('schedule_id', $request->schedule_id)
            ->exists();

        if ($alreadyExists) {
            return redirect()->back()->withErrors('User sudah terdaftar di kelas dan jadwal ini.');
        }

        $class->users()->attach($userId, [
            'schedule_id' => $request->schedule_id,
            'is_paid_per_session' => $request->has('is_paid_per_session'),
        ]);

        return redirect()->back()->with('success', 'User berhasil ditambahkan ke kelas.');
    }

    public function updateMembership(Request $request, Classes $class, User $user)
    {
        $data = $request->validate([
            'is_paid_per_session' => 'required|boolean',
        ]);

        $class->users()->updateExistingPivot($user->id, [
            'is_paid_per_session' => $data['is_paid_per_session'],
        ]);

        return redirect()->back()->with('success', 'Status membership berhasil diperbarui.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classes $class)
    {
        $coaches = User::where('role', 'pelatih')->get();
        return view('admin.class.edit', compact('class', 'coaches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classes $class)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sanggar_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'coach_id' => 'required|exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['name', 'sanggar_name', 'description', 'coach_id']);

        if ($request->hasFile('image')) {
            if ($class->image_path && \Storage::disk('public')->exists($class->image_path)) {
                \Storage::disk('public')->delete($class->image_path);
            }

            $imagePath = $request->file('image')->store('uploads/classes', 'public');
            $data['image_path'] = $imagePath;
        }

        $class->update($data);

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classes $class)
    {
        if ($class->image_path && \Storage::disk('public')->exists($class->image_path)) {
            \Storage::disk('public')->delete($class->image_path);
        }
        $class->delete();
        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil dihapus.');
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ClassesExport, 'classes.xlsx');
    }
}
