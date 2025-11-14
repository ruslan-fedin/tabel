@extends('layouts.admin')

@section('title', 'Просмотр табеля')

@section('content')
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold">Табель: {{ $timesheet->name }}</h2>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.timesheets.edit', $timesheet) }}"
                       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                        <i class="fas fa-edit mr-2"></i>Редактировать
                    </a>
                    <a href="{{ route('admin.timesheets.index') }}"
                       class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
                        Назад к списку
                    </a>
                </div>
            </div>
            <div class="mt-2 text-gray-600">
                Период: {{ $timesheet->start_date->format('d.m.Y') }} - {{ $timesheet->end_date->format('d.m.Y') }}
                ({{ $timesheet->days_count }} дней)
                @if($timesheet->note)
                    • Примечание: {{ $timesheet->note }}
                @endif
            </div>
        </div>

        <!-- Табель для просмотра -->
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead>
                <tr class="bg-gray-50">
                    <th class="border border-gray-200 px-4 py-3 text-left text-sm font-medium text-gray-700 sticky left-0 bg-gray-50 z-10">
                        №
                    </th>
                    <th class="border border-gray-200 px-4 py-3 text-left text-sm font-medium text-gray-700 sticky left-12 bg-gray-50 z-10">
                        ФИО / Должность
                    </th>
                    @foreach($timesheet->days_array as $dayNum => $day)
                        <th class="border border-gray-200 px-2 py-3 text-center text-sm font-medium text-gray-700 w-12">
                            {{ $dayNum }}<br>
                            <span class="text-xs text-gray-500">{{ $day['day'] }}</span>
                        </th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($timesheet->employees as $index => $employee)
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2 text-center sticky left-0 bg-white">
                            {{ $index + 1 }}
                        </td>
                        <td class="border border-gray-200 px-4 py-2 sticky left-12 bg-white min-w-64">
                            <div class="font-medium">{{ $employee->full_name }}</div>
                            <div class="text-sm text-gray-600">{{ $employee->position }}</div>
                        </td>

                        @foreach($timesheet->days_array as $dayNum => $day)
                            @php
                                $daysData = $employee->pivot->days_data ? json_decode($employee->pivot->days_data, true) : [];
                                $currentValue = $daysData[$dayNum] ?? '';
                                $cellClass = 'border border-gray-200 px-2 py-1 text-center ';

                                // Цвета для разных статусов
                                if ($currentValue === 'Я') $cellClass .= 'bg-green-100';
                                elseif ($currentValue === 'О') $cellClass .= 'bg-blue-100';
                                elseif ($currentValue === 'Б') $cellClass .= 'bg-yellow-100';
                                elseif ($currentValue === 'ОТ') $cellClass .= 'bg-purple-100';
                                elseif ($currentValue === 'У') $cellClass .= 'bg-red-100';
                            @endphp

                            <td class="{{ $cellClass }}">
                                {{ $currentValue }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Легенда -->
        <div class="p-6 border-t border-gray-200">
            <h3 class="text-lg font-medium mb-3">Легенда:</h3>
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center">
                    <div class="w-6 h-6 bg-green-100 border border-green-300 mr-2"></div>
                    <span>Я - Явка</span>
                </div>
                <div class="flex items-center">
                    <div class="w-6 h-6 bg-blue-100 border border-blue-300 mr-2"></div>
                    <span>О - Отпуск</span>
                </div>
                <div class="flex items-center">
                    <div class="w-6 h-6 bg-yellow-100 border border-yellow-300 mr-2"></div>
                    <span>Б - Больничный</span>
                </div>
                <div class="flex items-center">
                    <div class="w-6 h-6 bg-purple-100 border border-purple-300 mr-2"></div>
                    <span>ОТ - Отгул</span>
                </div>
                <div class="flex items-center">
                    <div class="w-6 h-6 bg-red-100 border border-red-300 mr-2"></div>
                    <span>У - Увольнение</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Статистика -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Всего сотрудников</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $timesheet->employees->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-calendar text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Период</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $timesheet->days_count }} дней</p>
                </div>
            </div>
        </div>
    </div>
@endsection
