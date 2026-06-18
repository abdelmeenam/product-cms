@php
    $navItems = [
        [
            'label' => 'Overview',
            'route' => 'overview',
            'icon' => 'home',
        ],
        [
            'label' => 'Products',
            'route' => 'products.index',
            'icon' => 'box',
        ],
        [
            'label' => 'Orders',
            'route' => 'orders.index',
            'icon' => 'cart',
        ],
    ];
@endphp

<aside class="hidden w-72 shrink-0 border-r border-slate-200 bg-white lg:flex lg:min-h-screen lg:flex-col">
    <div class="flex h-24 items-center gap-3 px-8">
        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/20">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                <path d="M12 3L20 7.5V16.5L12 21L4 16.5V7.5L12 3Z" stroke="currentColor" stroke-width="2"/>
                <path d="M12 12L20 7.5M12 12L4 7.5M12 12V21" stroke="currentColor" stroke-width="2"/>
            </svg>
        </div>
        <span class="text-2xl font-bold tracking-tight text-slate-950">CatalogueIQ</span>
    </div>

    <nav class="flex-1 space-y-2 px-4">
        @foreach($navItems as $item)
            @php
                $isActive = request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route']));
            @endphp

            <a
                href="{{ route($item['route']) }}"
                class="group flex items-center gap-4 rounded-2xl px-5 py-4 text-sm font-semibold transition
                {{ $isActive ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950' }}"
            >
                @if($item['icon'] === 'home')
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-width="2" d="M3 10.5L12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V10.5z"/>
                    </svg>
                @elseif($item['icon'] === 'box')
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-width="2" d="M21 8l-9-5-9 5 9 5 9-5z"/>
                        <path stroke-width="2" d="M3 8v8l9 5 9-5V8"/>
                        <path stroke-width="2" d="M12 13v8"/>
                    </svg>
                @else
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-width="2" d="M6 6h15l-2 8H8L6 6z"/>
                        <path stroke-width="2" d="M6 6L5 3H2"/>
                        <circle cx="9" cy="20" r="1.5"/>
                        <circle cx="18" cy="20" r="1.5"/>
                    </svg>
                @endif

                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="p-5">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-2xl bg-violet-50 text-violet-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="2" d="M18 10a6 6 0 0 0-12 0v4a3 3 0 0 0 3 3h1v-6H7v-1a5 5 0 0 1 10 0v1h-3v6h1a3 3 0 0 0 3-3v-4z"/>
                </svg>
            </div>
            <p class="font-bold text-slate-950">Need help?</p>
            <p class="mt-1 text-sm leading-6 text-slate-500">View documentation or contact our support team.</p>
            <a href="#" class="mt-4 flex items-center justify-center rounded-2xl border border-slate-200 px-4 py-3 text-sm font-bold text-blue-700 hover:bg-blue-50">
                Visit Help Center
                <span class="ml-2">→</span>
            </a>
        </div>
    </div>
</aside>
