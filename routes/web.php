<?php

use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\TimesheetController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    // Employees
    Route::resource('employees', EmployeeController::class);

    // Timesheets
    Route::resource('timesheets', TimesheetController::class);

    // Timesheet employees management
    Route::post('timesheets/{timesheet}/add-employee', [TimesheetController::class, 'addEmployee'])
        ->name('timesheets.add-employee');
    Route::post('timesheets/{timesheet}/bulk-add-employees', [TimesheetController::class, 'bulkAddEmployees'])
        ->name('timesheets.bulk-add-employees');
    Route::delete('timesheets/{timesheet}/employees/{employee}', [TimesheetController::class, 'removeEmployee'])
        ->name('timesheets.remove-employee');
    Route::post('timesheets/{timesheet}/employees/{employee}/update-day', [TimesheetController::class, 'updateDayData'])
        ->name('timesheets.update-day-data');
});

// Redirect to admin
Route::redirect('/', '/admin/timesheets');
