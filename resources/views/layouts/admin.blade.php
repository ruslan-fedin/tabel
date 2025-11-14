<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Админпанель</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="min-h-screen">
    <!-- Header -->
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
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
