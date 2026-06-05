@extends('role-workspaces.layouts.app')

@section('content')
    <section class="role-hero">
        <span class="role-eyebrow">{{ $eyebrow }}</span>
        <h2>{{ $label }} {{ __('Workspace') }}</h2>
        <p>{{ $summary }}</p>
    </section>

    <section class="role-metric-grid" aria-label="{{ __('Role metrics') }}">
        @foreach($metrics as $metric)
            <article class="role-card">
                <span class="role-card-icon"><i class="{{ $metric['icon'] }}"></i></span>
                <span>{{ __($metric['label']) }}</span>
                <strong>{{ $metric['value'] }}</strong>
            </article>
        @endforeach
    </section>

    <section class="role-action-grid" aria-label="{{ __('Role actions') }}">
        @foreach($actions as $action)
            <a class="role-action" href="{{ $action['url'] }}">
                <i class="{{ $action['icon'] }}"></i>
                <span>{{ __($action['label']) }}</span>
            </a>
        @endforeach
    </section>

    <section class="role-panel-grid">
        @foreach($panels as $panel)
            <article class="role-panel">
                <div class="role-panel-header">
                    <h2>{{ __($panel['title']) }}</h2>
                </div>
                <div class="role-list">
                    @forelse($panel['items'] as $item)
                        <div class="role-list-item">
                            <span class="role-list-icon"><i class="{{ $item['icon'] }}"></i></span>
                            <div class="role-list-copy">
                                <div class="role-list-title">{{ $item['title'] }}</div>
                                <div class="role-list-meta">
                                    {{ $item['meta'] }}@if($item['date']) - {{ $item['date'] }} @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="role-empty">{{ __('No items need attention right now.') }}</div>
                    @endforelse
                </div>
            </article>
        @endforeach
    </section>
@endsection