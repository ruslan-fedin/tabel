<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'position_id',
        'birth_date',
        'phone',
        'employment_date',
        'is_active'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'employment_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function getFullNameAttribute()
    {
        return trim($this->last_name . ' ' . $this->first_name . ' ' . ($this->middle_name ?? ''));
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function timesheets()
    {
        return $this->belongsToMany(Timesheet::class, 'timesheet_employees')
            ->withPivot('days_data', 'hours_data', 'row_color')
            ->withTimestamps();
    }

    // Исправленный расчет стажа работы
    public function getWorkExperienceAttribute()
    {
        if (!$this->employment_date) {
            return null;
        }

        $start = Carbon::parse($this->employment_date);
        $end = now();

        // Если дата приема в будущем
        if ($start->gt($end)) {
            return 'Дата приема в будущем';
        }

        $years = $start->diffInYears($end);
        $months = $start->copy()->addYears($years)->diffInMonths($end);

        $parts = [];
        if ($years > 0) {
            $parts[] = $years . ' ' . $this->getYearsText($years);
        }
        if ($months > 0) {
            $parts[] = $months . ' ' . $this->getMonthsText($months);
        }

        return $parts ? implode(' ', $parts) : 'Менее месяца';
    }

    private function getYearsText($years)
    {
        $cases = ['год', 'года', 'лет'];
        return $cases[($years % 100 > 4 && $years % 100 < 20) ? 2 : [2, 0, 1, 1, 1, 2][min($years % 10, 5)]];
    }

    private function getMonthsText($months)
    {
        $cases = ['месяц', 'месяца', 'месяцев'];
        return $cases[($months % 100 > 4 && $months % 100 < 20) ? 2 : [2, 0, 1, 1, 1, 2][min($months % 10, 5)]];
    }

    // Форматирование телефона
    public function getFormattedPhoneAttribute()
    {
        $phone = preg_replace('/\D/', '', $this->phone);

        if (strlen($phone) === 11) {
            return '+7 (' . substr($phone, 1, 3) . ') ' . substr($phone, 4, 3) . '-' . substr($phone, 7, 2) . '-' . substr($phone, 9, 2);
        }

        return $this->phone;
    }

    // Метод для установки employment_date
    public function setEmploymentDateAttribute($value)
    {
        $this->attributes['employment_date'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    // Метод для установки телефона
    public function setPhoneAttribute($value)
    {
        // Очищаем телефон от форматирования перед сохранением
        $this->attributes['phone'] = preg_replace('/\D/', '', $value);
    }
}
