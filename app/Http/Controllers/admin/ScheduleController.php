<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['class', 'class.coach'])->get();

        $events = $schedules->map(function ($schedule) {
            return [
                'id' => $schedule->id,
                'title' => $schedule->class->sanggar_name . ' ' . $schedule->class->coach->name . ' ' . $schedule->class->name,
                'start' => $schedule->schedule_date . 'T' . $schedule->start_time,
                'end' => $schedule->schedule_date . 'T' . $schedule->end_time,
                'extendedProps' => [
                    'class_name' => $schedule->class->name,
                    'coach_name' => $schedule->class->coach->name,
                    'sanggar_name' => $schedule->class->sanggar_name,
                    'day' => Carbon::parse($schedule->schedule_date)->translatedFormat('l'),
                    'start' => $schedule->start_time,
                    'end' => $schedule->end_time,
                ],
            ];
        });

        $classes = Classes::with('coach')->get();

        return view('admin.schedules.index', [
            'events' => $events,
            'classes' => $classes,
        ]);
    }

    public function store(Request $request): JsonResponse
    {

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'schedule_date' => 'required|date',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
        ]);

        // Validasi bentrok hanya untuk kelas yang sama
        $conflict = Schedule::where('class_id', $validated['class_id'])
            ->where('schedule_date', $validated['schedule_date'])
            ->where(function ($query) use ($validated) {
                $query->where(function ($q) use ($validated) {
                    $q->where('start_time', '<', $validated['end_time'])
                        ->where('end_time', '>', $validated['start_time']);
                });
            })
            ->exists();

        if ($conflict) {
            return response()->json([
                'message' => 'Jadwal bentrok dalam kelas ini.',
            ], 422);
        }

        $dayOfWeek = Carbon::parse($validated['schedule_date'])->format('l');

        $validated['day_of_week'] = $dayOfWeek;

        $schedule = Schedule::create($validated);

        return response()->json(['message' => 'Jadwal berhasil disimpan']);

    }


    // Update jadwal
    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $validated = $request->validate([
            'schedule_date' => 'required|date',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
        ]);

        $dayOfWeek = Carbon::parse($validated['schedule_date'])->format('l'); // Ambil nama hari

        $schedule->schedule_date = $validated['schedule_date'];
        $schedule->day_of_week = $dayOfWeek;
        $schedule->start_time = $validated['start_time'];
        $schedule->end_time = $validated['end_time'];
        $schedule->save();

        return response()->json([
            'id' => $schedule->id,
            'class_name' => $schedule->class->name,
            'coach_name' => $schedule->class->coach->name,
            'day_of_week' => $schedule->day_of_week,
            'start_time' => $schedule->start_time,
            'end_time' => $schedule->end_time,
        ]);
    }


    // Hapus jadwal
    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return response()->json(['message' => 'Jadwal berhasil dihapus']);
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SchedulesExport, 'schedules.xlsx');
    }
}
