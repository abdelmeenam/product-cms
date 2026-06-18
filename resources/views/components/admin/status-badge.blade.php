@props(['status'])

@php
    $classes = match ($status) {
        'active', 'paid', 'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'pending', 'draft' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'cancelled', 'inactive' => 'bg-rose-50 text-rose-700 ring-rose-200',
        default => 'bg-slate-100 text-slate-600 ring-slate-200',
    };
@endphp

<span class="inline-flex items-center rounded-xl px-3 py-1 text-xs font-bold capitalize ring-1 {{ $classes }}">
    {{ str_replace('_', ' ', $status) }}
</span>
