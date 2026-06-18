<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', config('app.name'))</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen bg-stone-100 text-stone-950 antialiased">
        <div class="absolute inset-x-0 top-0 -z-10 h-80 bg-linear-to-b from-teal-200/60 via-amber-100/40 to-transparent"></div>

        <div class="mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-6 sm:px-6 lg:px-8">
            <header class="mb-6 rounded-[2rem] border border-white/60 bg-white/85 p-4 shadow-lg shadow-stone-200/60 backdrop-blur">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-medium uppercase tracking-[0.25em] text-teal-700">Product CMS</p>
                        <h1 class="mt-1 text-2xl font-semibold text-stone-950">@yield('heading', 'Commerce Overview')</h1>
                    </div>

                    <nav class="flex flex-wrap gap-2 text-sm font-medium">
                        <a
                            href="{{ route('overview') }}"
                            @class([
                                'rounded-full px-4 py-2 transition',
                                'bg-stone-950 text-white shadow-md' => request()->routeIs('overview'),
                                'bg-stone-100 text-stone-700 hover:bg-stone-200' => ! request()->routeIs('overview'),
                            ])
                        >
                            Overview
                        </a>
                        <a
                            href="{{ route('products.index') }}"
                            @class([
                                'rounded-full px-4 py-2 transition',
                                'bg-stone-950 text-white shadow-md' => request()->routeIs('products.*'),
                                'bg-stone-100 text-stone-700 hover:bg-stone-200' => ! request()->routeIs('products.*'),
                            ])
                        >
                            Products
                        </a>
                        <a
                            href="{{ route('orders.index') }}"
                            @class([
                                'rounded-full px-4 py-2 transition',
                                'bg-stone-950 text-white shadow-md' => request()->routeIs('orders.*'),
                                'bg-stone-100 text-stone-700 hover:bg-stone-200' => ! request()->routeIs('orders.*'),
                            ])
                        >
                            Orders
                        </a>
                    </nav>
                </div>
            </header>

            @if (session('success'))
                <div class="mb-6 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <main class="flex-1">
                @yield('content')
            </main>
        </div>
    </body>
</html>
