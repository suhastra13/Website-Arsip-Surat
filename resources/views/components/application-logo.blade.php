@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2']) }}>
    <img
        src="{{ asset('img/logosumsel.png') }}"
        alt="Logo Arsip Surat"
        class="h-8 w-auto">
    <span class="font-semibold text-base tracking-tight">
        Arsip Surat
    </span>
</div>