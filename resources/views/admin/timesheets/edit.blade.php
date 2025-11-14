@extends('layouts.admin')

@section('title', 'Редактирование табеля')

@section('content')
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold">Табель: {{ $timesheet->name }}</h2>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.timesheets.index') }}"
                       class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
                        Назад
                    </a>
                </div>
            </div>
            <div class="mt-2 text-gray-600">
                Период: {{ $timesheet->start_date->format('d.m.Y') }} - {{ $timesheet->end_date->format('d.m.Y') }}
                ({{ $timesheet->days_count }} дней)
            </div>
        </div>

        <!-- Управление сотрудниками -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-wrap gap-4 items-end mb-4">
                <!-- Добавление одного сотрудника -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Добавить сотрудника</label>
                    <form action="{{ route('admin.timesheets.add-employee', $timesheet) }}" method="POST" class="flex gap-2">
                        @csrf
                        <select name="employee_id" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Выберите сотрудника</option>
                            @foreach($employees->where('is_active', true) as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->position }})</option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                            Добавить
                        </button>
                    </form>
                </div>

                <!-- Массовое добавление -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Массовое добавление</label>
                    <form action="{{ route('admin.timesheets.bulk-add-employees', $timesheet) }}" method="POST" class="flex gap-2">
                        @csrf
                        <select name="employee_ids[]" multiple
                                class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-64"
                                size="4">
                            @foreach($employees->where('is_active', true) as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->position }})</option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                            Добавить выбранных
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Табель -->
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
                    <th class="border border-gray-200 px-2 py-3 text-center text-sm font-medium text-gray-700">
                        Примечание
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
                            <form action="{{ route('admin.timesheets.remove-employee', [$timesheet, $employee]) }}"
                                  method="POST" class="mt-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:text-red-900 text-xs"
                                        onclick="return confirm('Удалить сотрудника из табеля?')">
                                    <i class="fas fa-times mr-1"></i>Удалить
                                </button>
                            </form>
                        </td>
                        <td class="border border-gray-200 px-4 py-2">
                            <input type="text" class="w-full border-none focus:ring-0 p-0 text-sm"
                                   placeholder="Примечание...">
                        </td>

                        @foreach($timesheet->days_array as $dayNum => $day)
                            @php
                                $daysData = $employee->pivot->days_data ? json_decode($employee->pivot->days_data, true) : [];
                                $currentValue = $daysData[$dayNum] ?? '';
                                $cellClass = 'border border-gray-200 px-2 py-1 text-center cursor-pointer ';

                                // Цвета для разных статусов
                                if ($currentValue === 'Я') $cellClass .= 'bg-green-100';
                                elseif ($currentValue === 'О') $cellClass .= 'bg-blue-100';
                                elseif ($currentValue === 'Б') $cellClass .= 'bg-yellow-100';
                                elseif ($currentValue === 'ОТ') $cellClass .= 'bg-purple-100';
                                elseif ($currentValue === 'У') $cellClass .= 'bg-red-100';
                            @endphp

                            <td class="{{ $cellClass }} day-cell"
                                data-employee="{{ $employee->id }}"
                                data-day="{{ $dayNum }}"
                                onclick="editDay(this)">
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

    <!-- Модальное окно для редактирования дня -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-80">
            <h3 class="text-lg font-medium mb-4">Редактирование дня</h3>
            <div class="grid grid-cols-3 gap-2 mb-4">
                <button type="button" onclick="setDayValue('Я')" class="p-2 bg-green-100 rounded hover:bg-green-200">Я</button>
                <button type="button" onclick="setDayValue('О')" class="p-2 bg-blue-100 rounded hover:bg-blue-200">О</button>
                <button type="button" onclick="setDayValue('Б')" class="p-2 bg-yellow-100 rounded hover:bg-yellow-200">Б</button>
                <button type="button" onclick="setDayValue('ОТ')" class="p-2 bg-purple-100 rounded hover:bg-purple-200">ОТ</button>
                <button type="button" onclick="setDayValue('У')" class="p-2 bg-red-100 rounded hover:bg-red-200">У</button>
                <button type="button" onclick="setDayValue('')" class="p-2 bg-gray-100 rounded hover:bg-gray-200">Очистить</button>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Отмена</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentCell = null;
        let currentEmployeeId = null;
        let currentDay = null;

        function editDay(cell) {
            currentCell = cell;
            currentEmployeeId = cell.getAttribute('data-employee');
            currentDay = cell.getAttribute('data-day');

            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
            currentCell = null;
        }

        function setDayValue(value) {
            if (!currentCell) return;

            // Обновляем отображение
            currentCell.textContent = value;

            // Обновляем классы цвета
            currentCell.className = 'border border-gray-200 px-2 py-1 text-center cursor-pointer ';
            if (value === 'Я') currentCell.classList.add('bg-green-100');
            else if (value === 'О') currentCell.classList.add('bg-blue-100');
            else if (value === 'Б') currentCell.classList.add('bg-yellow-100');
            else if (value === 'ОТ') currentCell.classList.add('bg-purple-100');
            else if (value === 'У') currentCell.classList.add('bg-red-100');

            // Сохраняем на сервер
            fetch('{{ route("admin.timesheets.update-day-data", [$timesheet, '_EMPLOYEE_']) }}'.replace('_EMPLOYEE_', currentEmployeeId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    day: currentDay,
                    value: value
                })
            });

            closeModal();
        }

        // Закрытие модального окна по клику вне его
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
@endpush
