@extends('layouts.admin')

@section('title', 'Редактирование табеля')

@section('content')
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold">Табель: {{ $timesheet->name }}</h2>
                <div class="flex space-x-2">
                    <button type="button" onclick="openBulkUpdateModal()"
                            class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition">
                        <i class="fas fa-edit mr-2"></i>Массовое заполнение
                    </button>
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

        <!-- Общая статистика -->
        <div class="px-6 py-4 bg-blue-50 border-b border-blue-200">
            <h3 class="text-lg font-medium text-blue-900 mb-2">Общая статистика табеля</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4 text-sm">
                <div class="text-center">
                    <div class="font-semibold text-blue-800">Сотрудников</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $totalStats['total_employees'] }}</div>
                </div>
                <div class="text-center">
                    <div class="font-semibold text-green-800">Явка</div>
                    <div class="text-2xl font-bold text-green-600">{{ $totalStats['Я'] }}</div>
                </div>
                <div class="text-center">
                    <div class="font-semibold text-indigo-800">Центр</div>
                    <div class="text-2xl font-bold text-indigo-600">{{ $totalStats['Ц'] }}</div>
                </div>
                <div class="text-center">
                    <div class="font-semibold text-blue-800">Отпуск</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $totalStats['О'] }}</div>
                </div>
                <div class="text-center">
                    <div class="font-semibold text-yellow-800">Больничный</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $totalStats['Б'] }}</div>
                </div>
                <div class="text-center">
                    <div class="font-semibold text-purple-800">Отгул</div>
                    <div class="text-2xl font-bold text-purple-600">{{ $totalStats['ОТ'] }}</div>
                </div>
                <div class="text-center">
                    <div class="font-semibold text-red-800">Увольнение</div>
                    <div class="text-2xl font-bold text-red-600">{{ $totalStats['У'] }}</div>
                </div>
                <div class="text-center">
                    <div class="font-semibold text-gray-800">Часы</div>
                    <div class="text-2xl font-bold text-gray-600">{{ $totalStats['total_hours'] }}</div>
                </div>
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

        <!-- Основная таблица табеля -->
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
                    <th class="border border-gray-200 px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50">
                        Примечание
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($timesheet->employees as $index => $employee)
                    @php
                        $rowColor = $employee->pivot->row_color ?? '#ffffff';
                        $stats = $employeeStats[$employee->id] ?? null;
                    @endphp
                    <tr class="hover:bg-gray-50 employee-row"
                        data-employee-id="{{ $employee->id }}"
                        style="background-color: {{ $rowColor }}"
                        ondblclick="selectRow(this)">
                        <td class="border border-gray-200 px-4 py-2 text-center sticky left-0 bg-white">
                            {{ $index + 1 }}
                        </td>
                        <td class="border border-gray-200 px-4 py-2 sticky left-12 bg-white min-w-64">
                            <div class="font-medium">{{ $employee->full_name }}</div>
                            <div class="text-sm text-gray-600">{{ $employee->position }}</div>
                            <div class="flex space-x-2 mt-1">
                                <button type="button"
                                        onclick="changeRowColor({{ $employee->id }})"
                                        class="text-xs text-blue-600 hover:text-blue-800"
                                        title="Изменить цвет строки">
                                    <i class="fas fa-palette mr-1"></i>Цвет
                                </button>
                                <form action="{{ route('admin.timesheets.remove-employee', [$timesheet, $employee]) }}"
                                      method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs text-red-600 hover:text-red-800"
                                            onclick="return confirm('Удалить сотрудника из табеля?')">
                                        <i class="fas fa-times mr-1"></i>Удалить
                                    </button>
                                </form>
                            </div>
                        </td>

                        @foreach($timesheet->days_array as $dayNum => $day)
                            @php
                                $daysData = $employee->pivot->days_data ? json_decode($employee->pivot->days_data, true) : [];
                                $hoursData = $employee->pivot->hours_data ? json_decode($employee->pivot->hours_data, true) : [];
                                $currentStatus = $daysData[$dayNum] ?? '';
                                $currentHours = $hoursData[$dayNum] ?? '';
                                $cellClass = 'border border-gray-200 px-2 py-1 text-center cursor-pointer ';

                                if ($currentStatus === 'Я') $cellClass .= 'bg-green-100';
                                elseif ($currentStatus === 'О') $cellClass .= 'bg-blue-100';
                                elseif ($currentStatus === 'Б') $cellClass .= 'bg-yellow-100';
                                elseif ($currentStatus === 'ОТ') $cellClass .= 'bg-purple-100';
                                elseif ($currentStatus === 'У') $cellClass .= 'bg-red-100';
                                elseif ($currentStatus === 'Ц') $cellClass .= 'bg-indigo-100';
                            @endphp

                            <td class="{{ $cellClass }} day-cell"
                                data-employee="{{ $employee->id }}"
                                data-day="{{ $dayNum }}"
                                data-status="{{ $currentStatus }}"
                                data-hours="{{ $currentHours }}"
                                onclick="editDay(this)">
                                @if($currentStatus)
                                    <div class="font-medium">{{ $currentStatus }}</div>
                                    @if($currentHours)
                                        <div class="text-xs text-gray-600">{{ $currentHours }}ч</div>
                                    @endif
                                @endif
                            </td>
                        @endforeach

                        <td class="border border-gray-200 px-4 py-2 bg-white">
                            <input type="text" class="w-full border-none focus:ring-0 p-0 text-sm"
                                   placeholder="Примечание...">
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Компактная статистика по сотрудникам -->
    <div class="mt-6 bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-semibold">Статистика по сотрудникам</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Сотрудник</th>
                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Я</th>
                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ц</th>
                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">О</th>
                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Б</th>
                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">ОТ</th>
                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">У</th>
                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Дней</th>
                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Часов</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($timesheet->employees as $employee)
                    @php
                        $stats = $employeeStats[$employee->id] ?? null;
                        $rowColor = $employee->pivot->row_color ?? 'transparent';
                    @endphp
                    @if($stats)
                        <tr class="hover:bg-gray-50" style="border-left: 4px solid {{ $rowColor }};">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $employee->full_name }}</div>
                                <div class="text-xs text-gray-500">{{ $employee->position }}</div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap text-center">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            {{ $stats['Я'] }}
                        </span>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap text-center">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                            {{ $stats['Ц'] }}
                        </span>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap text-center">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $stats['О'] }}
                        </span>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap text-center">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            {{ $stats['Б'] }}
                        </span>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap text-center">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                            {{ $stats['ОТ'] }}
                        </span>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap text-center">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                            {{ $stats['У'] }}
                        </span>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap text-center text-sm font-semibold text-gray-900">
                                {{ $stats['total_days'] }}
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap text-center text-sm font-semibold text-gray-900">
                                {{ $stats['total_hours'] }}
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Легенда -->
    <div class="mt-6 bg-white rounded-lg shadow-md p-4">
        <div class="flex flex-wrap items-center gap-4 text-sm">
            <span class="font-medium text-gray-700">Легенда:</span>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-100 border border-green-300 mr-1"></div>
                <span>Я - Явка</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-indigo-100 border border-indigo-300 mr-1"></div>
                <span>Ц - Центр</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-blue-100 border border-blue-300 mr-1"></div>
                <span>О - Отпуск</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-yellow-100 border border-yellow-300 mr-1"></div>
                <span>Б - Больничный</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-purple-100 border border-purple-300 mr-1"></div>
                <span>ОТ - Отгул</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-red-100 border border-red-300 mr-1"></div>
                <span>У - Увольнение</span>
            </div>
        </div>
        <div class="mt-2 text-xs text-gray-600">
            <p><strong>Подсказка:</strong> Дважды щелкните по строке сотрудника для выделения, кликните по ячейке дня для редактирования</p>
        </div>
    </div>

    <!-- Модальное окно для редактирования дня -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-80">
            <h3 class="text-lg font-medium mb-4">Редактирование дня</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Статус</label>
                <div class="grid grid-cols-3 gap-2" id="statusButtons">
                    <button type="button" onclick="setDayStatus('Я')" class="p-2 bg-green-100 rounded hover:bg-green-200 status-btn" data-status="Я">Я</button>
                    <button type="button" onclick="setDayStatus('О')" class="p-2 bg-blue-100 rounded hover:bg-blue-200 status-btn" data-status="О">О</button>
                    <button type="button" onclick="setDayStatus('Б')" class="p-2 bg-yellow-100 rounded hover:bg-yellow-200 status-btn" data-status="Б">Б</button>
                    <button type="button" onclick="setDayStatus('ОТ')" class="p-2 bg-purple-100 rounded hover:bg-purple-200 status-btn" data-status="ОТ">ОТ</button>
                    <button type="button" onclick="setDayStatus('У')" class="p-2 bg-red-100 rounded hover:bg-red-200 status-btn" data-status="У">У</button>
                    <button type="button" onclick="setDayStatus('Ц')" class="p-2 bg-indigo-100 rounded hover:bg-indigo-200 status-btn" data-status="Ц">Ц</button>
                    <button type="button" onclick="setDayStatus('')" class="p-2 bg-gray-100 rounded hover:bg-gray-200 status-btn" data-status="">Очистить</button>
                </div>
            </div>
            <div class="mb-4">
                <label for="hoursInput" class="block text-sm font-medium text-gray-700 mb-2">Часы работы</label>
                <input type="number" id="hoursInput" step="0.5" min="0" max="24"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="0">
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Отмена</button>
                <button type="button" onclick="saveDayData()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Сохранить</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно для массового заполнения -->
    <div id="bulkUpdateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-96">
            <h3 class="text-lg font-medium mb-4">Массовое заполнение</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Сотрудники</label>
                    <select id="bulkEmployeeIds" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" size="5">
                        @foreach($timesheet->employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                        @endforeach
                    </select>
                    <div class="mt-1">
                        <button type="button" onclick="selectAllEmployees()" class="text-xs text-blue-600 hover:text-blue-800">Выбрать всех</button>
                        <button type="button" onclick="deselectAllEmployees()" class="text-xs text-gray-600 hover:text-gray-800 ml-2">Снять выделение</button>
                        <button type="button" onclick="selectHighlightedRows()" class="text-xs text-green-600 hover:text-green-800 ml-2">Выбрать выделенные</button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">С какого дня</label>
                        <input type="number" id="bulkStartDay" min="1" max="{{ $timesheet->days_count }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">По какой день</label>
                        <input type="number" id="bulkEndDay" min="1" max="{{ $timesheet->days_count }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="{{ $timesheet->days_count }}">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Статус</label>
                    <select id="bulkStatus" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Я">Явка</option>
                        <option value="О">Отпуск</option>
                        <option value="Б">Больничный</option>
                        <option value="ОТ">Отгул</option>
                        <option value="У">Увольнение</option>
                        <option value="Ц">Центр</option>
                    </select>
                </div>

                <div>
                    <label for="bulkHours" class="block text-sm font-medium text-gray-700 mb-2">Часы работы</label>
                    <input type="number" id="bulkHours" step="0.5" min="0" max="24"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Оставьте пустым, чтобы не менять">
                </div>
            </div>
            <div class="flex justify-end space-x-2 mt-6">
                <button type="button" onclick="closeBulkUpdateModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Отмена</button>
                <button type="button" onclick="applyBulkUpdate()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Применить</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно для выбора цвета строки -->
    <div id="colorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-80">
            <h3 class="text-lg font-medium mb-4">Выберите цвет строки</h3>
            <div class="mb-4">
                <input type="color" id="rowColorPicker" value="#ffffff" class="w-full h-12 cursor-pointer">
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeColorModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Отмена</button>
                <button type="button" onclick="saveRowColor()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Сохранить</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentCell = null;
        let currentEmployeeId = null;
        let currentDay = null;
        let currentStatus = '';
        let currentHours = '';
        let selectedEmployeeId = null;
        let selectedRows = new Set();

        function editDay(cell) {
            currentCell = cell;
            currentEmployeeId = cell.getAttribute('data-employee');
            currentDay = cell.getAttribute('data-day');
            currentStatus = cell.getAttribute('data-status') || '';
            currentHours = cell.getAttribute('data-hours') || '';

            // Установить текущий статус в модальном окне
            document.querySelectorAll('.status-btn').forEach(btn => {
                btn.classList.remove('ring-2', 'ring-blue-500');
                if (btn.getAttribute('data-status') === currentStatus) {
                    btn.classList.add('ring-2', 'ring-blue-500');
                }
            });

            document.getElementById('hoursInput').value = currentHours;
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function setDayStatus(status) {
            currentStatus = status;

            // Обновить визуальное выделение кнопок
            document.querySelectorAll('.status-btn').forEach(btn => {
                btn.classList.remove('ring-2', 'ring-blue-500');
                if (btn.getAttribute('data-status') === status) {
                    btn.classList.add('ring-2', 'ring-blue-500');
                }
            });
        }

        function saveDayData() {
            if (!currentCell || !currentEmployeeId) return;

            currentHours = document.getElementById('hoursInput').value;

            // Обновляем отображение
            let content = '';
            if (currentStatus) {
                content = `<div class="font-medium">${currentStatus}</div>`;
                if (currentHours) {
                    content += `<div class="text-xs text-gray-600">${currentHours}ч</div>`;
                }
            }
            currentCell.innerHTML = content;

            // Обновляем классы цвета
            updateCellColor(currentCell, currentStatus);

            // Сохраняем на сервер
            fetch('{{ route("admin.timesheets.update-day-data", $timesheet) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    employee_id: currentEmployeeId,
                    day: currentDay,
                    status: currentStatus,
                    hours: currentHours || null
                })
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    closeModal();
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Ошибка при сохранении данных');
            });
        }

        function updateCellColor(cell, status) {
            // Убираем все цветовые классы
            cell.className = 'border border-gray-200 px-2 py-1 text-center cursor-pointer ';

            // Добавляем цвет в зависимости от статуса
            if (status === 'Я') cell.classList.add('bg-green-100');
            else if (status === 'О') cell.classList.add('bg-blue-100');
            else if (status === 'Б') cell.classList.add('bg-yellow-100');
            else if (status === 'ОТ') cell.classList.add('bg-purple-100');
            else if (status === 'У') cell.classList.add('bg-red-100');
            else if (status === 'Ц') cell.classList.add('bg-indigo-100');
        }

        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
            currentCell = null;
            currentEmployeeId = null;
        }

        function changeRowColor(employeeId) {
            selectedEmployeeId = employeeId;
            const row = document.querySelector(`tr[data-employee-id="${employeeId}"]`);
            const currentColor = row ? row.style.backgroundColor : '#ffffff';
            document.getElementById('rowColorPicker').value = rgbToHex(currentColor);
            document.getElementById('colorModal').classList.remove('hidden');
            document.getElementById('colorModal').classList.add('flex');
        }

        function saveRowColor() {
            if (!selectedEmployeeId) return;

            const color = document.getElementById('rowColorPicker').value;

            fetch('{{ route("admin.timesheets.update-row-color", $timesheet) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    employee_id: selectedEmployeeId,
                    color: color
                })
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    // Обновляем цвет строки
                    const row = document.querySelector(`tr[data-employee-id="${selectedEmployeeId}"]`);
                    if (row) {
                        row.style.backgroundColor = color;
                    }
                    closeColorModal();
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Ошибка при сохранении цвета');
            });
        }

        function closeColorModal() {
            document.getElementById('colorModal').classList.add('hidden');
            document.getElementById('colorModal').classList.remove('flex');
            selectedEmployeeId = null;
        }

        // Функция для преобразования RGB в HEX
        function rgbToHex(rgb) {
            if (!rgb || rgb === '' || rgb === 'transparent') return '#ffffff';

            if (rgb.startsWith('#')) return rgb;

            const result = rgb.match(/\d+/g);
            if (!result) return '#ffffff';

            return '#' + result.map(x => {
                const hex = parseInt(x).toString(16);
                return hex.length === 1 ? '0' + hex : hex;
            }).join('');
        }

        // Выделение строки
        function selectRow(row) {
            const employeeId = row.getAttribute('data-employee-id');

            if (selectedRows.has(employeeId)) {
                // Снимаем выделение
                row.style.boxShadow = 'none';
                selectedRows.delete(employeeId);
            } else {
                // Выделяем строку
                row.style.boxShadow = 'inset 0 0 0 2px #3b82f6';
                selectedRows.add(employeeId);
            }

            updateBulkModalSelection();
        }

        // Массовое заполнение
        function openBulkUpdateModal() {
            updateBulkModalSelection();
            document.getElementById('bulkUpdateModal').classList.remove('hidden');
            document.getElementById('bulkUpdateModal').classList.add('flex');
        }

        function closeBulkUpdateModal() {
            document.getElementById('bulkUpdateModal').classList.add('hidden');
            document.getElementById('bulkUpdateModal').classList.remove('flex');
        }

        function selectAllEmployees() {
            const select = document.getElementById('bulkEmployeeIds');
            for (let i = 0; i < select.options.length; i++) {
                select.options[i].selected = true;
            }
        }

        function deselectAllEmployees() {
            const select = document.getElementById('bulkEmployeeIds');
            for (let i = 0; i < select.options.length; i++) {
                select.options[i].selected = false;
            }
        }

        function selectHighlightedRows() {
            const select = document.getElementById('bulkEmployeeIds');
            for (let i = 0; i < select.options.length; i++) {
                select.options[i].selected = selectedRows.has(select.options[i].value);
            }
        }

        function updateBulkModalSelection() {
            const select = document.getElementById('bulkEmployeeIds');
            for (let i = 0; i < select.options.length; i++) {
                if (selectedRows.has(select.options[i].value)) {
                    select.options[i].selected = true;
                }
            }
        }

        function applyBulkUpdate() {
            const employeeIds = Array.from(document.getElementById('bulkEmployeeIds').selectedOptions).map(opt => opt.value);
            const startDay = parseInt(document.getElementById('bulkStartDay').value);
            const endDay = parseInt(document.getElementById('bulkEndDay').value);
            const status = document.getElementById('bulkStatus').value;
            const hours = document.getElementById('bulkHours').value;

            if (employeeIds.length === 0) {
                alert('Выберите хотя бы одного сотрудника');
                return;
            }

            if (startDay > endDay) {
                alert('Начальный день не может быть больше конечного');
                return;
            }

            const days = [];
            for (let day = startDay; day <= endDay; day++) {
                days.push(day);
            }

            const data = {
                employee_ids: employeeIds,
                days: days,
                status: status,
                hours: hours || null
            };

            fetch('{{ route("admin.timesheets.bulk-update-days", $timesheet) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    closeBulkUpdateModal();
                    location.reload();
                } else {
                    alert('Ошибка при массовом обновлении');
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Ошибка при массовом обновлении');
            });
        }

        // Закрытие модальных окон по клику вне их
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        document.getElementById('bulkUpdateModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBulkUpdateModal();
            }
        });

        document.getElementById('colorModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeColorModal();
            }
        });
    </script>
@endpush
