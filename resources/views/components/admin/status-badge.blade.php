@props([
    'status' => null,
    'classes' => null,
    'label' => null,
])

@php($presentation = \App\View\Components\Admin\StatusBadge::present($status))

<span class="inline-flex items-center rounded-xl px-3 py-1 text-xs font-bold ring-1 {{ $classes ?? $presentation['classes'] }}">
    {{ $label ?? $presentation['label'] }}
</span>
