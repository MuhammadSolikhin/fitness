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
        return view('admin.class.show', compact('class'));
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
}
