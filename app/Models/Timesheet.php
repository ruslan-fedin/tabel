<?php

// app/Models/Timesheet.php

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
            ->withPivot('days_data')
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
}
