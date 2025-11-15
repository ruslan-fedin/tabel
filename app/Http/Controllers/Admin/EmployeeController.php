<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use App\Exports\EmployeesExport;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('position');

        // Поиск
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('last_name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('position', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Фильтрация по статусу
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $employees = $query->latest()->paginate(20);

        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        $positions = Position::where('is_active', true)->orderBy('name')->get();
        return view('admin.employees.create', compact('positions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'position_id' => 'required|exists:positions,id',
            'birth_date' => 'required|date',
            'phone' => 'required|string|max:20',
            'employment_date' => 'nullable|date',
            'is_active' => 'sometimes|boolean'
        ]);

        // Устанавливаем is_active
        $validated['is_active'] = $request->has('is_active');

        Employee::create($validated);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Сотрудник успешно создан.');
    }

    public function show(Employee $employee)
    {
        $employee->load('position');
        return view('admin.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $positions = Position::where('is_active', true)->orderBy('name')->get();
        $employee->load('position');
        return view('admin.employees.edit', compact('employee', 'positions'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'position_id' => 'required|exists:positions,id',
            'birth_date' => 'required|date',
            'phone' => 'required|string|max:20',
            'employment_date' => 'nullable|date',
            'is_active' => 'sometimes|boolean'
        ]);

        // Устанавливаем is_active
        $validated['is_active'] = $request->has('is_active');

        $employee->update($validated);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Данные сотрудника обновлены.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('admin.employees.index')
            ->with('success', 'Сотрудник удален.');
    }

    public function export()
    {
        return Excel::download(new EmployeesExport, 'employees_' . date('Y-m-d') . '.xlsx');
    }
}
