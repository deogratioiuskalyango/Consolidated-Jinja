@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core::card>
        <x-core::card.header>
            <x-core::card.title>{{ __('Hardware quotation requests') }}</x-core::card.title>
        </x-core::card.header>

        <x-core::table>
            <x-core::table.header>
                <x-core::table.header.cell>{{ __('Client') }}</x-core::table.header.cell>
                <x-core::table.header.cell>{{ __('Contact') }}</x-core::table.header.cell>
                <x-core::table.header.cell>{{ __('Project') }}</x-core::table.header.cell>
                <x-core::table.header.cell>{{ __('Materials') }}</x-core::table.header.cell>
                <x-core::table.header.cell>{{ __('Status') }}</x-core::table.header.cell>
                <x-core::table.header.cell>{{ __('Date') }}</x-core::table.header.cell>
            </x-core::table.header>
            <x-core::table.body>
                @forelse ($quotes as $quote)
                    <x-core::table.body.row>
                        <x-core::table.body.cell>
                            <strong>{{ $quote->name }}</strong>
                            <div>{{ $quote->delivery_location }}</div>
                        </x-core::table.body.cell>
                        <x-core::table.body.cell>
                            <div>{{ $quote->email }}</div>
                            <div>{{ $quote->phone }}</div>
                        </x-core::table.body.cell>
                        <x-core::table.body.cell>
                            <div>{{ $quote->project_type }}</div>
                            <div>{{ $quote->budget }}</div>
                            <div>{{ $quote->needed_by }}</div>
                        </x-core::table.body.cell>
                        <x-core::table.body.cell style="max-width: 420px; white-space: pre-wrap">{{ $quote->materials }}</x-core::table.body.cell>
                        <x-core::table.body.cell>{{ ucfirst($quote->status) }}</x-core::table.body.cell>
                        <x-core::table.body.cell>{{ BaseHelper::formatDate($quote->created_at) }}</x-core::table.body.cell>
                    </x-core::table.body.row>
                @empty
                    <x-core::table.body.row>
                        <x-core::table.body.cell colspan="6">{{ __('No quotation requests yet.') }}</x-core::table.body.cell>
                    </x-core::table.body.row>
                @endforelse
            </x-core::table.body>
        </x-core::table>

        @if ($quotes->hasPages())
            <x-core::card.footer>
                {{ $quotes->links() }}
            </x-core::card.footer>
        @endif
    </x-core::card>
@endsection
