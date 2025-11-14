@extends('layouts.admin')

@section('title', $employee->full_name)

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold">Карточка сотрудника</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.employees.edit', $employee) }}"
                           class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                            <i class="fas fa-edit mr-2"></i>Редактировать
                        </a>
                        <a href="{{ route('admin.employees.index') }}"
                           class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
                            Назад
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Основная информация -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Основная информация</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Фамилия</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->last_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Имя</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->first_name }}</dd>
                            </div>
                            @if($employee->middle_name)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Отчество</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->middle_name }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Должность</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->position }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Контактная информация -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Контактная информация</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Телефон</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->phone }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Дата рождения</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->birth_date->format('d.m.Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Возраст</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->birth_date->age }} лет</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Статус</dt>
                                <dd class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $employee->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $employee->is_active ? 'Активный' : 'Неактивный' }}
                                </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- История табелей -->
        <div class="mt-6 bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium">Участие в табелях</h3>
            </div>
            <div class="p-6">
                @if($employee->timesheets->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Табель</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Период</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дней</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($employee->timesheets as $timesheet)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('admin.timesheets.show', $timesheet) }}"
                                           class="text-blue-600 hover:text-blue-900 font-medium">
                                            {{ $timesheet->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                        {{ $timesheet->start_date->format('d.m.Y') }} - {{ $timesheet->end_date->format('d.m.Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $timesheet->days_count }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                        <p>Сотрудник не участвовал ни в одном табеле</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
