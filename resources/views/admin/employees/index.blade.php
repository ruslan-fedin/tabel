@extends('layouts.admin')

@section('title', 'Сотрудники')

@section('content')
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold">Сотрудники</h2>
                <a href="{{ route('admin.employees.create') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i>Добавить сотрудника
                </a>
            </div>
        </div>

        <!-- Фильтры и поиск -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Поиск</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="w-64 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="ФИО или должность...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Все</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Активные</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Неактивные</option>
                    </select>
                </div>
                <button type="submit"
                        class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
                    Применить
                </button>
                <a href="{{ route('admin.employees.index') }}"
                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition">
                    Сбросить
                </a>
            </form>
        </div>

        <!-- Таблица -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ФИО</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Должность</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Телефон</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата рождения</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($employees as $employee)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $employee->full_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $employee->position }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $employee->phone }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $employee->birth_date->format('d.m.Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $employee->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $employee->is_active ? 'Активный' : 'Неактивный' }}
                        </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.employees.show', $employee) }}"
                               class="text-blue-600 hover:text-blue-900 mr-3"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.employees.edit', $employee) }}"
                               class="text-green-600 hover:text-green-900 mr-3"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Удалить сотрудника?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Пагинация -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $employees->links() }}
        </div>
    </div>
@endsection
