@props(['name', 'class' => 'w-5 h-5'])

@php
    $svgPath = "components.svg.{$name}";
@endphp

@if(View::exists($svgPath))
    <span {{ $attributes->merge(['class' => $class . ' inline-flex']) }}>
        @include($svgPath)
    </span>
@else
    <span {{ $attributes->merge(['class' => $class . ' inline-flex text-gray-400']) }}>
        <!-- Icon not found: {{ $name }} -->
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-full h-full">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
        </svg>
    </span>
@endif