<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur">
    <div class="flex h-20 items-center justify-between gap-4 px-6 lg:px-8">

        <div class="ml-auto flex items-center gap-3">
            <button class="relative flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50">
                <span class="absolute right-3 top-3 h-2 w-2 rounded-full bg-blue-600"></span>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 0 1-6 0"/>
                </svg>
            </button>

            <a href="{{ route('overview') }}" class="flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="2" d="M4 19h16M7 16V9m5 7V5m5 11v-4"/>
                </svg>
            </a>
        </div>
    </div>
</header>
