@once
    <ul class="breadcrumb">
        @foreach ($crumbs = Theme::breadcrumb()->getCrumbs() as $i => $crumb)
            @if (! $loop->last)
                <li class="breadcrumb-item">
                    <a href="{{ $crumb['url'] }}" title="{{ $crumb['label'] }}">
                        {{ $crumb['label'] }}
                    </a>
                </li>
            @else
                <li class="breadcrumb-item active">
                    {{ $crumb['label'] }}
                </li>
            @endif
        @endforeach
    </ul>
@endonce
