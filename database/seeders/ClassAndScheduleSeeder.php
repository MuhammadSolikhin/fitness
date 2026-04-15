<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classes;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;

class ClassAndScheduleSeeder extends Seeder
{

    public function run(): void
    {
        $coaches = User::where('role', 'pelatih')->get();

        if ($coaches->count() < 4) {
            $this->command->error('Minimal harus ada 4 pelatih untuk seeder ini.');
            return;
        }

        $selectedCoaches = $coaches->random(4)->values();

        $assignedDays = ['Wednesday', 'Thursday', 'Friday', 'Saturday'];

        $startDate = Carbon::now()->startOfWeek(); // mulai dari minggu ini
        $totalWeeks = 12; // 3 bulan kira-kira

        $classes = [];

        for ($i = 0; $i < 4; $i++) {
            $class = Classes::create([
                'name' => "Kelas Spesial " . chr(65 + $i),
                'sanggar_name' => 'Sanggar ' . chr(65 + $i),
                'description' => "Kelas dengan jadwal setiap {$assignedDays[$i]}",
                'coach_id' => $selectedCoaches[$i]->id,
            ]);
            $classes[] = [
                'model' => $class,
                'day' => $assignedDays[$i],
            ];
        }

        // Jam mulai tiap kelas (3 jam durasi)
        $startHours = [7, 12, 17, 7]; // kelas D di hari Sabtu jam 7 pagi

        for ($week = 0; $week < $totalWeeks; $week++) {
            foreach ($classes as $index => $item) {
                $day = $item['day'];
                $class = $item['model'];

                // Cari tanggal di minggu ini sesuai hari
                $dayOfWeekNumber = [
                    'Sunday' => 0,
                    'Monday' => 1,
                    'Tuesday' => 2,
                    'Wednesday' => 3,
                    'Thursday' => 4,
                    'Friday' => 5,
                    'Saturday' => 6,
                ][$day];

                // Start minggu ini ditambah minggu ke-$week
                $weekStart = $startDate->copy()->addWeeks($week);
                $currentDay = $weekStart->dayOfWeek;

                if ($currentDay <= $dayOfWeekNumber) {
                    $scheduledDate = $weekStart->copy()->addDays($dayOfWeekNumber - $currentDay);
                } else {
                    $scheduledDate = $weekStart->copy()->addDays(7 - ($currentDay - $dayOfWeekNumber));
                }

                // Pastikan tanggal tidak di masa lalu
                if ($scheduledDate->lt(Carbon::today())) {
                    continue;
                }

                $startHour = $startHours[$index];
                $startTime = Carbon::createFromTime($startHour, 0, 0);
                $endTime = $startTime->copy()->addHours(3);

                Schedule::create([
                    'class_id' => $class->id,
                    'day_of_week' => $day,
                    'schedule_date' => $scheduledDate->format('Y-m-d'),
                    'start_time' => $startTime->format('H:i:s'),
                    'end_time' => $endTime->format('H:i:s'),
                ]);
            }
        }
    }




}
