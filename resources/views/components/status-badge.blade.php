@props(['status'])

@php
    $classes = match ($status->value) {
        'menunggu_bayar' => 'bg-amber-100 text-amber-800 border-amber-200',
        'diproses' => 'bg-blue-100 text-blue-800 border-blue-200',
        'siap', 'selesai' => 'bg-teal-100 text-teal-800 border-teal-200',
        default => 'bg-slate-100 text-slate-600 border-slate-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {$classes}"]) }}>
    {{ $status->label() }}
</span>
