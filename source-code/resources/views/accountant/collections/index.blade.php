@php $navCollectionsActiveClass = 'active mm-active'; @endphp
@extends('accountant.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">

                {{-- Page Title & Breadcrumb --}}
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between border-bottom mb-20">
                            <div class="page-title-left">
                                <h3 class="mb-sm-0">{{ __('Rent Collections') }}</h3>
                            </div>
                            <div class="page-title-right">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                    <li class="breadcrumb-item active">{{ __('Rent Collections') }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="row mb-3">
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a href="{{ route('accountant.collections.create') }}" class="btn btn-primary">
                            <i class="ri-add-line me-1"></i> {{ __('Record Payment') }}
                        </a>
                        <a href="#" class="btn btn-outline-secondary">
                            <i class="ri-download-line me-1"></i> {{ __('Export CSV') }}
                        </a>
                    </div>
                </div>

                {{-- Filter Form --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-body">
                                <form method="GET" action="{{ route('accountant.collections.index') }}">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-2">
                                            <label class="form-label">{{ __('Status') }}</label>
                                            <select name="status" class="form-select form-select-sm">
                                                <option value="">{{ __('All Statuses') }}</option>
                                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
                                                <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                                <option value="reversed"  {{ request('status') == 'reversed'  ? 'selected' : '' }}>{{ __('Reversed') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">{{ __('Payment Method') }}</label>
                                            <select name="payment_method" class="form-select form-select-sm">
                                                <option value="">{{ __('All Methods') }}</option>
                                                <option value="cash"   {{ request('payment_method') == 'cash'   ? 'selected' : '' }}>{{ __('Cash') }}</option>
                                                <option value="bank"   {{ request('payment_method') == 'bank'   ? 'selected' : '' }}>{{ __('Bank Transfer') }}</option>
                                                <option value="mtn"    {{ request('payment_method') == 'mtn'    ? 'selected' : '' }}>{{ __('MTN Mobile Money') }}</option>
                                                <option value="airtel" {{ request('payment_method') == 'airtel' ? 'selected' : '' }}>{{ __('Airtel Money') }}</option>
                                                <option value="cheque" {{ request('payment_method') == 'cheque' ? 'selected' : '' }}>{{ __('Cheque') }}</option>
                                                <option value="card"   {{ request('payment_method') == 'card'   ? 'selected' : '' }}>{{ __('Card') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">{{ __('From') }}</label>
                                            <input type="date" name="period_from" class="form-control form-control-sm" value="{{ request('period_from') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">{{ __('To') }}</label>
                                            <input type="date" name="period_to" class="form-control form-control-sm" value="{{ request('period_to') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                                <i class="ri-filter-line me-1"></i> {{ __('Filter') }}
                                            </button>
                                        </div>
                                        <div class="col-md-2">
                                            <a href="{{ route('accountant.collections.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                                                {{ __('Reset') }}
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DataTable --}}
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="collectionsTable" class="table table-bordered table-striped align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('SL') }}</th>
                                        <th>{{ __('Receipt #') }}</th>
                                        <th>{{ __('Tenant') }}</th>
                                        <th>{{ __('Property') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Method') }}</th>
                                        <th>{{ __('Amount (UGX)') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($collections as $index => $c)
                                    <tr>
                                        <td>{{ $collections->firstItem() + $index }}</td>
                                        <td>{{ $c->receipt_number ?? '—' }}</td>
                                        <td>{{ $c->tenant->name ?? '—' }}</td>
                                        <td>{{ $c->property->name ?? '—' }}</td>
                                        <td>{{ $collectionTypes[$c->collection_type] ?? '—' }}</td>
                                        <td>{{ $paymentMethods[$c->payment_method] ?? '—' }}</td>
                                        <td>{{ number_format($c->amount) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($c->payment_date)->format('d M Y') }}</td>
                                        <td>
                                            @if($c->status == 'confirmed')
                                                <span class="badge bg-success">{{ __('Confirmed') }}</span>
                                            @elseif($c->status == 'pending')
                                                <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                            @elseif($c->status == 'reversed')
                                                <span class="badge bg-danger">{{ __('Reversed') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($c->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-inline-flex gap-1">
                                                <a href="{{ route('accountant.collections.show', $c) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="{{ __('View') }}">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                                <a href="{{ route('accountant.collections.receipt', $c) }}"
                                                   class="btn btn-sm btn-outline-secondary"
                                                   title="{{ __('Receipt') }}" target="_blank">
                                                    <i class="ri-file-text-line"></i>
                                                </a>
                                                @if($c->status == 'confirmed')
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger collection-reverse-btn"
                                                        data-id="{{ $c->id }}"
                                                        data-receipt="{{ $c->receipt_number }}"
                                                        data-url="{{ route('accountant.collections.reverse', $c) }}"
                                                        title="{{ __('Reverse') }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#reverseModal">
                                                    <i class="ri-arrow-go-back-line"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $collections->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Reverse Payment Modal --}}
<div class="modal fade" id="reverseModal" tabindex="-1" aria-labelledby="reverseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reverseModalLabel">{{ __('Reverse Payment') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <form id="reverseForm" method="POST" class="ajax" data-handler="reverseShowMessage">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3">
                        {{ __('You are about to reverse receipt:') }}
                        <strong id="reverseReceiptNumber"></strong>
                    </p>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Reason for Reversal') }} <span class="text-danger">*</span></label>
                        <textarea name="reverse_reason" class="form-control" rows="3" required
                                  placeholder="{{ __('Enter reason for reversing this payment...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Confirm Reversal') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('style')
    @include('common.layouts.datatable-style')
@endpush

@push('script')
    @include('common.layouts.datatable-script')
    <script>
        $(document).ready(function () {
            $('#collectionsTable').DataTable({
                responsive: true,
                pageLength: 25,
                order: [[7, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [9] }
                ]
            });

            // Populate reverse modal
            $(document).on('click', '.collection-reverse-btn', function () {
                var url = $(this).data('url');
                var receipt = $(this).data('receipt');
                $('#reverseForm').attr('action', url);
                $('#reverseReceiptNumber').text(receipt);
            });
        });

        window.reverseShowMessage = function (response) {
            if (response.status === 'success') {
                window.location.reload();
            } else {
                commonHandler(response);
            }
        };
    </script>
@endpush
