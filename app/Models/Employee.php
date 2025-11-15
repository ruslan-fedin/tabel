<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'birth_date',
        'phone',
        'position',
        'is_active'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function getFullNameAttribute()
    {
        return trim($this->last_name . ' ' . $this->first_name . ' ' . ($this->middle_name ?? ''));
    }

    public function timesheets()
    {
        return $this->belongsToMany(Timesheet::class, 'timesheet_employees')
            ->withPivot('days_data', 'hours_data', 'row_color')
            ->withTimestamps();
    }
}
