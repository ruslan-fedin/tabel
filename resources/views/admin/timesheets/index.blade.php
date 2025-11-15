@extends('layouts.admin')

@section('title', 'Табели рабочего времени')

@section('content')
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold">Табели рабочего времени</h2>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.timesheets.export.excel') }}"
                       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                        <i class="fas fa-file-excel mr-2"></i>Экспорт в Excel
                    </a>
                    <a href="{{ route('admin.timesheets.create') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-2"></i>Создать табель
                    </a>
                </div>
            </div>
        </div>

        <!-- Таблица -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Период</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Кол-во дней</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Сотрудников</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Примечание</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($timesheets as $timesheet)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $timesheet->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                            {{ $timesheet->start_date->format('d.m.Y') }} - {{ $timesheet->end_date->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $timesheet->days_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $timesheet->employees_count }}</td>
                        <td class="px-6 py-4 text-gray-600">
                            <div class="max-w-xs truncate">{{ $timesheet->note }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.timesheets.show', $timesheet) }}"
                               class="text-blue-600 hover:text-blue-900 mr-3" title="Просмотр">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.timesheets.edit', $timesheet) }}"
                               class="text-green-600 hover:text-green-900 mr-3" title="Редактировать">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.timesheets.destroy', $timesheet) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Удалить табель?')"
                                        title="Удалить">
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
            {{ $timesheets->links() }}
        </div>
    </div>

    @if($timesheets->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center mt-6">
            <i class="fas fa-inbox text-yellow-400 text-4xl mb-3"></i>
            <h3 class="text-lg font-medium text-yellow-800 mb-2">Табели не найдены</h3>
            <p class="text-yellow-700 mb-4">Создайте первый табель для учета рабочего времени</p>
            <a href="{{ route('admin.timesheets.create') }}"
               class="bg-yellow-600 text-white px-6 py-2 rounded hover:bg-yellow-700 transition">
                Создать табель
            </a>
        </div>
    @endif
@endsection
