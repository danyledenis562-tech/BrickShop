@props([
    'items' => [],
])

@if (count($items))
    <nav class="mb-6 text-sm text-[color:var(--muted)]" aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-2">
            @foreach ($items as $i => $item)
                <li class="flex items-center gap-2">
                    @if ($i > 0)
                        <span aria-hidden="true">/</span>
                    @endif
                    @if (! empty($item['url']) && $i < count($items) - 1)
                        <a href="{{ $item['url'] }}" class="font-medium text-[color:var(--lego-blue)] hover:underline">{{ $item['label'] }}</a>
                    @else
                        <span class="font-semibold text-[color:var(--text-main)]">{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
