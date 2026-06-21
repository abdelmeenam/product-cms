<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'CatalogueIQ' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-950 antialiased">
    <div class="min-h-screen lg:flex">
        @include('components.admin.sidebar')

        <div class="flex-1">
            @include('components.admin.topbar')

            <main class="px-6 py-8 lg:px-8">
                @if(session('success'))
                    <div
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700"
                    >
                        <div class="flex items-center justify-between gap-4">
                            <span>{{ session('success') }}</span>
                            <button type="button" @click="show = false" class="text-emerald-600">×</button>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
