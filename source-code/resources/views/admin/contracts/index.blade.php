@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="page-content-wrapper bg-white p-30 radius-20">

                {{-- Page Header --}}
                <div class="d-sm-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                    <div>
                        <h3 class="mb-1">{{ __('Contracts') }}</h3>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('Contracts') }}</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.contracts.create') }}" class="btn btn-primary">
                        <i class="ri-add-line me-1"></i>{{ __('Create Contract') }}
                    </a>
                </div>

                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Contracts Table --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('Reference') }}</th>
                                <th>{{ __('Tenant') }}</th>
                                <th>{{ __('Property') }}</th>
                                <th>{{ __('Monthly Rent') }}</th>
                                <th>{{ __('Period') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contracts as $contract)
                                <tr>
                                    <td>{{ $loop->iteration + ($contracts->currentPage() - 1) * $contracts->perPage() }}</td>
                                    <td>
                                        <span class="fw-medium">{{ $contract->reference_no }}</span>
                                    </td>
                                    <td>
                                        @if($contract->tenant && $contract->tenant->user)
                                            {{ $contract->tenant->user->first_name }} {{ $contract->tenant->user->last_name }}
                                        @else
                                            {{ $contract->tenant_name ?? '—' }}
                                        @endif
                                    </td>
                                    <td>{{ $contract->property_name ?? ($contract->property?->name ?? '—') }}</td>
                                    <td>
                                        <span class="fw-medium">
                                            UGX {{ number_format($contract->monthly_rent, 0) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $contract->commencement_date ? $contract->commencement_date->format('d M Y') : '—' }}
                                            &ndash;
                                            {{ $contract->expiry_date ? $contract->expiry_date->format('d M Y') : '—' }}
                                        </small>
                                    </td>
                                    <td>
                                        @php
                                            $statusMap = [
                                                'draft'             => 'secondary',
                                                'sent'              => 'info',
                                                'reviewing'         => 'warning',
                                                'disputed'          => 'danger',
                                                'pending_signature' => 'primary',
                                                'signed'            => 'success',
                                                'active'            => 'success',
                                                'terminated'        => 'dark',
                                            ];
                                            $badgeColor = $statusMap[$contract->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }}">
                                            {{ ucwords(str_replace('_', ' ', $contract->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            {{-- View --}}
                                            <a href="{{ route('admin.contracts.show', $contract->id) }}"
                                               class="btn btn-sm btn-outline-info" title="{{ __('View') }}">
                                                <i class="ri-eye-line"></i>
                                            </a>

                                            {{-- Send (only if draft) --}}
                                            @if($contract->status === 'draft')
                                                <form action="{{ route('admin.contracts.send', $contract->id) }}"
                                                      method="POST" style="display:inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-primary"
                                                            title="{{ __('Send to Tenant') }}"
                                                            onclick="return confirm('{{ __('Send this contract to the tenant?') }}')">
                                                        <i class="ri-send-plane-line"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- Delete --}}
                                            <form action="{{ route('admin.contracts.destroy', $contract->id) }}"
                                                  method="POST" style="display:inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        title="{{ __('Delete') }}"
                                                        onclick="return confirm('{{ __('Are you sure you want to delete this contract?') }}')">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="ri-file-list-3-line fs-2 d-block mb-2"></i>
                                        {{ __('No contracts found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($contracts->hasPages())
                    <div class="d-flex justify-content-end mt-3">
                        {{ $contracts->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
