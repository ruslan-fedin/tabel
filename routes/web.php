<?php

use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\TimesheetController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    // Employees
    Route::resource('employees', EmployeeController::class);
    Route::get('employees/export/excel', [EmployeeController::class, 'export'])->name('employees.export.excel');

    // Timesheets
    Route::resource('timesheets', TimesheetController::class);
    Route::get('timesheets/export/excel', [TimesheetController::class, 'export'])->name('timesheets.export.excel');

    // Timesheet employees management
    Route::post('timesheets/{timesheet}/add-employee', [TimesheetController::class, 'addEmployee'])
        ->name('timesheets.add-employee');
    Route::post('timesheets/{timesheet}/bulk-add-employees', [TimesheetController::class, 'bulkAddEmployees'])
        ->name('timesheets.bulk-add-employees');
    Route::delete('timesheets/{timesheet}/employees/{employee}', [TimesheetController::class, 'removeEmployee'])
        ->name('timesheets.remove-employee');

    // New routes for enhanced functionality
    Route::post('timesheets/{timesheet}/update-day', [TimesheetController::class, 'updateDayData'])
        ->name('timesheets.update-day-data');
    Route::post('timesheets/{timesheet}/update-row-color', [TimesheetController::class, 'updateRowColor'])
        ->name('timesheets.update-row-color');
    Route::post('timesheets/{timesheet}/bulk-update-days', [TimesheetController::class, 'bulkUpdateDays'])
        ->name('timesheets.bulk-update-days');
});

// Redirect to admin
Route::redirect('/', '/admin/timesheets');

Route::resource('positions', PositionController::class);

Route::prefix('admin')->name('admin.')->group(function () {
    // Employees
    Route::resource('employees', EmployeeController::class);
    Route::get('employees/export/excel', [EmployeeController::class, 'export'])->name('employees.export.excel');

    // Positions
    Route::resource('positions', PositionController::class);

    // Timesheets
    Route::resource('timesheets', TimesheetController::class);
    Route::get('timesheets/export/excel', [TimesheetController::class, 'export'])->name('timesheets.export.excel');

    // ... остальные маршруты
});
