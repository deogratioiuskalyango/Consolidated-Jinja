@extends('role-workspaces.layouts.app')

@section('content')
    <section class="role-hero">
        <span class="role-eyebrow">{{ $label }}</span>
        <h2>{{ Str::title(str_replace('-', ' ', $section)) }}</h2>
        <p>{{ __('Focused workspace for the selected role task. Use the sidebar or action shortcuts to move through related workflows.') }}</p>
    </section>

    <section class="role-action-grid" aria-label="{{ __('Related actions') }}">
        @foreach($actions as $action)
            <a class="role-action" href="{{ $action['url'] }}">
                <i class="{{ $action['icon'] }}"></i>
                <span>{{ __($action['label']) }}</span>
            </a>
        @endforeach
    </section>

    <section class="role-panel role-workbench-panel">
        <div class="role-panel-header">
            <h2>{{ Str::title(str_replace('-', ' ', $section)) }}</h2>
        </div>
        <div class="role-list role-workbench-list">
            @forelse($items as $item)
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
                <div class="role-empty">{{ __('There are no records for this workspace yet.') }}</div>
            @endforelse
        </div>
    </section>
@endsection