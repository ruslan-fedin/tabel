<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Timesheet;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TimesheetController extends Controller
{
    public function index()
    {
        $timesheets = Timesheet::withCount('employees')->latest()->paginate(20);
        return view('admin.timesheets.index', compact('timesheets'));
    }

    public function create()
    {
        return view('admin.timesheets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'note' => 'nullable|string'
        ]);

        $timesheet = Timesheet::create($validated);

        return redirect()->route('admin.timesheets.edit', $timesheet)
            ->with('success', 'Табель создан. Теперь вы можете добавить сотрудников.');
    }

    public function show(Timesheet $timesheet)
    {
        $timesheet->load('employees');
        return view('admin.timesheets.show', compact('timesheet'));
    }

    public function edit(Timesheet $timesheet)
    {
        $employees = Employee::where('is_active', true)->get();
        $timesheet->load('employees');

        // Получаем статистику для каждого сотрудника
        $employeeStats = [];
        foreach ($timesheet->employees as $employee) {
            $employeeStats[$employee->id] = $timesheet->getEmployeeStats($employee->id);
        }

        // Общая статистика по табелю
        $totalStats = $timesheet->getTotalStats();

        return view('admin.timesheets.edit', compact('timesheet', 'employees', 'employeeStats', 'totalStats'));
    }

    public function update(Request $request, Timesheet $timesheet)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'note' => 'nullable|string'
        ]);

        $timesheet->update($validated);

        return redirect()->route('admin.timesheets.index')
            ->with('success', 'Табель обновлен.');
    }

    public function destroy(Timesheet $timesheet)
    {
        $timesheet->delete();

        return redirect()->route('admin.timesheets.index')
            ->with('success', 'Табель удален.');
    }

    public function addEmployee(Request $request, Timesheet $timesheet)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id'
        ]);

        if (!$timesheet->employees()->where('employee_id', $request->employee_id)->exists()) {
            $timesheet->employees()->attach($request->employee_id);
        }

        return back()->with('success', 'Сотрудник добавлен в табель.');
    }

    public function removeEmployee(Timesheet $timesheet, Employee $employee)
    {
        $timesheet->employees()->detach($employee->id);

        return back()->with('success', 'Сотрудник удален из табеля.');
    }

    public function bulkAddEmployees(Request $request, Timesheet $timesheet)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id'
        ]);

        foreach ($request->employee_ids as $employeeId) {
            if (!$timesheet->employees()->where('employee_id', $employeeId)->exists()) {
                $timesheet->employees()->attach($employeeId);
            }
        }

        return back()->with('success', 'Сотрудники массово добавлены в табель.');
    }

    public function updateRowColor(Request $request, Timesheet $timesheet, Employee $employee)
    {
        $request->validate([
            'color' => 'required|string|max:7'
        ]);

        $timesheet->employees()->updateExistingPivot($employee->id, [
            'row_color' => $request->color
        ]);

        return response()->json(['success' => true]);
    }

    public function bulkUpdateDays(Request $request, Timesheet $timesheet)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'days' => 'required|array',
            'days.*' => 'integer|min:1',
            'status' => 'required|string|max:2',
            'hours' => 'nullable|numeric|min:0|max:24'
        ]);

        foreach ($request->employee_ids as $employeeId) {
            $pivot = $timesheet->employees()->where('employee_id', $employeeId)->first()->pivot;
            $daysData = $pivot->days_data ? json_decode($pivot->days_data, true) : [];
            $hoursData = $pivot->hours_data ? json_decode($pivot->hours_data, true) : [];

            foreach ($request->days as $day) {
                $daysData[$day] = $request->status;
                if ($request->has('hours') && $request->hours !== '') {
                    $hoursData[$day] = $request->hours;
                }
            }

            $timesheet->employees()->updateExistingPivot($employeeId, [
                'days_data' => json_encode($daysData),
                'hours_data' => json_encode($hoursData)
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function updateDayData(Request $request, Timesheet $timesheet, Employee $employee)
    {
        $request->validate([
            'day' => 'required|integer|min:1',
            'status' => 'required|string|max:2',
            'hours' => 'nullable|numeric|min:0|max:24'
        ]);

        $pivot = $timesheet->employees()->where('employee_id', $employee->id)->first()->pivot;
        $daysData = $pivot->days_data ? json_decode($pivot->days_data, true) : [];
        $hoursData = $pivot->hours_data ? json_decode($pivot->hours_data, true) : [];

        $daysData[$request->day] = $request->status;
        if ($request->has('hours') && $request->hours !== '') {
            $hoursData[$request->day] = $request->hours;
        }

        $timesheet->employees()->updateExistingPivot($employee->id, [
            'days_data' => json_encode($daysData),
            'hours_data' => json_encode($hoursData)
        ]);

        return response()->json(['success' => true]);
    }
}
