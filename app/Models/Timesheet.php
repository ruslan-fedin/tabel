<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Timesheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'note'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'timesheet_employees')
            ->withPivot('days_data', 'hours_data', 'row_color')
            ->withTimestamps();
    }

    public function getDaysCountAttribute()
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getDaysArrayAttribute()
    {
        $days = [];
        $current = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);

        $dayNumber = 1;
        while ($current <= $end) {
            $days[$dayNumber] = [
                'date' => $current->format('Y-m-d'),
                'day' => $current->format('d'),
                'weekday' => $current->dayOfWeek
            ];
            $current->addDay();
            $dayNumber++;
        }

        return $days;
    }

    // Метод для получения статистики по сотруднику
    public function getEmployeeStats($employeeId)
    {
        $employee = $this->employees()->where('employee_id', $employeeId)->first();
        if (!$employee) return null;

        $daysData = $employee->pivot->days_data ? json_decode($employee->pivot->days_data, true) : [];

        $stats = [
            'Я' => 0, // Явка
            'Ц' => 0, // Центр
            'О' => 0, // Отпуск
            'Б' => 0, // Больничный
            'ОТ' => 0, // Отгул
            'У' => 0, // Увольнение
            'total_days' => 0,
            'total_hours' => 0
        ];

        $hoursData = $employee->pivot->hours_data ? json_decode($employee->pivot->hours_data, true) : [];

        foreach ($daysData as $day => $status) {
            if (isset($stats[$status])) {
                $stats[$status]++;
                $stats['total_days']++;

                // Суммируем часы работы
                if (isset($hoursData[$day]) && is_numeric($hoursData[$day])) {
                    $stats['total_hours'] += floatval($hoursData[$day]);
                }
            }
        }

        return $stats;
    }

    // Метод для получения общей статистики по табелю
    public function getTotalStats()
    {
        $totalStats = [
            'Я' => 0,
            'Ц' => 0,
            'О' => 0,
            'Б' => 0,
            'ОТ' => 0,
            'У' => 0,
            'total_employees' => $this->employees->count(),
            'total_days' => 0,
            'total_hours' => 0
        ];

        foreach ($this->employees as $employee) {
            $employeeStats = $this->getEmployeeStats($employee->id);
            if ($employeeStats) {
                foreach ($employeeStats as $key => $value) {
                    if (isset($totalStats[$key])) {
                        $totalStats[$key] += $value;
                    }
                }
            }
        }

        return $totalStats;
    }
}
