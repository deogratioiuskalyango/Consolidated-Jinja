@extends('tenant.layouts.app')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="page-content-wrapper bg-white p-30 radius-20">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between border-bottom mb-20">
                                <div class="page-title-left">
                                    <h3 class="mb-sm-0">{{ $pageTitle }}</h3>
                                </div>
                                <div class="page-title-right">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a>
                                        </li>
                                        <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($contracts->isEmpty())
                        <div class="alert alert-info d-flex align-items-center gap-2" role="alert">
                            <i class="ri-information-line fs-5"></i>
                            <span>{{ __('No contracts have been issued to you yet.') }}</span>
                        </div>
                    @else
                        <div class="table-responsive bg-off-white theme-border radius-4 p-25">
                            <table class="table bg-off-white theme-border p-20">
                                <thead>
                                    <tr>
                                        <th>{{ __('SL') }}</th>
                                        <th>{{ __('Reference') }}</th>
                                        <th>{{ __('Property') }}</th>
                                        <th>{{ __('Monthly Rent') }}</th>
                                        <th>{{ __('Period') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contracts as $contract)
                                        <tr>
                                            <td>{{ $loop->iteration + ($contracts->currentPage() - 1) * $contracts->perPage() }}</td>
                                            <td><strong>{{ $contract->reference_no }}</strong></td>
                                            <td>
                                                {{ $contract->property_name }}
                                                @if($contract->unit_name)
                                                    <br><small class="text-muted">{{ $contract->unit_name }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $contract->currency }} {{ number_format($contract->monthly_rent, 0) }}</strong>
                                            </td>
                                            <td>
                                                @if($contract->commencement_date)
                                                    {{ \Carbon\Carbon::parse($contract->commencement_date)->format('d M Y') }}
                                                @endif
                                                @if($contract->expiry_date)
                                                    <span class="text-muted">–</span>
                                                    {{ \Carbon\Carbon::parse($contract->expiry_date)->format('d M Y') }}
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $statusConfig = [
                                                        'draft'             => ['bg-secondary', 'Draft'],
                                                        'sent'              => ['bg-info text-dark', 'Sent'],
                                                        'reviewing'         => ['bg-warning text-dark', 'Reviewing'],
                                                        'disputed'          => ['bg-danger', 'Disputed'],
                                                        'pending_signature' => ['bg-primary', 'Pending Signature'],
                                                        'signed'            => ['bg-success', 'Signed'],
                                                        'active'            => ['bg-success', 'Active'],
                                                        'terminated'        => ['bg-dark', 'Terminated'],
                                                    ];
                                                    [$badgeClass, $label] = $statusConfig[$contract->status] ?? ['bg-secondary', ucfirst($contract->status)];
                                                @endphp
                                                <span class="badge {{ $badgeClass }} px-3 py-2">{{ __($label) }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('tenant.contracts.show', $contract->id) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="ri-eye-line me-1"></i>{{ __('View') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 d-flex justify-content-end">
                            {{ $contracts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
