<nav class="bg-blue-600 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-4">
                <h1 class="text-xl font-bold">Табель учета рабочего времени</h1>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('admin.timesheets.index') }}"
                   class="hover:bg-blue-700 px-3 py-2 rounded transition {{ request()->routeIs('admin.timesheets.*') ? 'bg-blue-700' : '' }}">Табели</a>
                <a href="{{ route('admin.employees.index') }}"
                   class="hover:bg-blue-700 px-3 py-2 rounded transition {{ request()->routeIs('admin.employees.*') ? 'bg-blue-700' : '' }}">Сотрудники</a>
                <a href="{{ route('admin.positions.index') }}"
                   class="hover:bg-blue-700 px-3 py-2 rounded transition {{ request()->routeIs('admin.positions.*') ? 'bg-blue-700' : '' }}">Должности</a>
            </div>
        </div>
    </div>
</nav>
