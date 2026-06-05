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
                                <h3 class="mb-sm-0">{{ __('Collection Detail') }}</h3>
                            </div>
                            <div class="page-title-right">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('accountant.collections.index') }}">{{ __('Collections') }}</a></li>
                                    <li class="breadcrumb-item active">{{ __('View') }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">

                    {{-- Left: Collection Details --}}
                    <div class="col-lg-8">
                        <div class="card border shadow-sm">
                            <div class="card-header bg-light d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">{{ __('Payment Information') }}</h5>
                                @if($collection->status == 'confirmed')
                                    <span class="badge bg-success">{{ __('Confirmed') }}</span>
                                @elseif($collection->status == 'pending')
                                    <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                @elseif($collection->status == 'reversed')
                                    <span class="badge bg-danger">{{ __('Reversed') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($collection->status) }}</span>
                                @endif
                            </div>
                            <div class="card-body">
                                <dl class="row mb-0">
                                    <dt class="col-sm-4 text-muted">{{ __('Receipt Number') }}</dt>
                                    <dd class="col-sm-8 fw-semibold">{{ $collection->receipt_number ?? '—' }}</dd>

                                    <dt class="col-sm-4 text-muted">{{ __('Tenant') }}</dt>
                                    <dd class="col-sm-8">{{ $collection->tenant->name ?? '—' }}</dd>

                                    <dt class="col-sm-4 text-muted">{{ __('Property') }}</dt>
                                    <dd class="col-sm-8">{{ $collection->property->name ?? '—' }}</dd>

                                    <dt class="col-sm-4 text-muted">{{ __('Unit') }}</dt>
                                    <dd class="col-sm-8">{{ $collection->unit->unit_number ?? ($collection->unit->name ?? '—') }}</dd>

                                    <dt class="col-sm-4 text-muted">{{ __('Collection Type') }}</dt>
                                    <dd class="col-sm-8">{{ $collectionTypes[$collection->collection_type] ?? '—' }}</dd>

                                    <dt class="col-sm-4 text-muted">{{ __('Payment Method') }}</dt>
                                    <dd class="col-sm-8">{{ $paymentMethods[$collection->payment_method] ?? '—' }}</dd>

                                    <dt class="col-sm-4 text-muted">{{ __('Amount') }}</dt>
                                    <dd class="col-sm-8 fw-bold fs-5">UGX {{ number_format($collection->amount) }}</dd>

                                    <dt class="col-sm-4 text-muted">{{ __('Transaction Reference') }}</dt>
                                    <dd class="col-sm-8">{{ $collection->transaction_ref ?? '—' }}</dd>

                                    <dt class="col-sm-4 text-muted">{{ __('Payment Date') }}</dt>
                                    <dd class="col-sm-8">{{ \Carbon\Carbon::parse($collection->payment_date)->format('d M Y') }}</dd>

                                    <dt class="col-sm-4 text-muted">{{ __('Notes') }}</dt>
                                    <dd class="col-sm-8">{{ $collection->notes ?? '—' }}</dd>

                                    <dt class="col-sm-4 text-muted">{{ __('Collected By') }}</dt>
                                    <dd class="col-sm-8">{{ $collection->collectedBy->name ?? '—' }}</dd>

                                    @if($collection->status == 'reversed')
                                    <dt class="col-sm-4 text-muted">{{ __('Reversed By') }}</dt>
                                    <dd class="col-sm-8">{{ $collection->reversedBy->name ?? '—' }}</dd>

                                    <dt class="col-sm-4 text-muted">{{ __('Reversal Reason') }}</dt>
                                    <dd class="col-sm-8">{{ $collection->reverse_reason ?? '—' }}</dd>

                                    <dt class="col-sm-4 text-muted">{{ __('Reversed At') }}</dt>
                                    <dd class="col-sm-8">{{ $collection->reversed_at ? \Carbon\Carbon::parse($collection->reversed_at)->format('d M Y H:i') : '—' }}</dd>
                                    @endif

                                    <dt class="col-sm-4 text-muted">{{ __('Recorded At') }}</dt>
                                    <dd class="col-sm-8">{{ $collection->created_at->format('d M Y H:i') }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    {{-- Right: Actions --}}
                    <div class="col-lg-4">
                        <div class="card border shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">{{ __('Actions') }}</h5>
                            </div>
                            <div class="card-body d-grid gap-2">
                                <a href="{{ route('accountant.collections.receipt', $collection) }}"
                                   class="btn btn-outline-primary"
                                   target="_blank">
                                    <i class="ri-printer-line me-1"></i> {{ __('Print Receipt') }}
                                </a>

                                @if($collection->status == 'confirmed')
                                <button type="button"
                                        class="btn btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#reverseModal">
                                    <i class="ri-arrow-go-back-line me-1"></i> {{ __('Reverse Payment') }}
                                </button>
                                @endif

                                <a href="{{ route('accountant.collections.index') }}" class="btn btn-outline-secondary">
                                    <i class="ri-arrow-left-line me-1"></i> {{ __('Back to List') }}
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

{{-- Reverse Payment Modal --}}
@if($collection->status == 'confirmed')
<div class="modal fade" id="reverseModal" tabindex="-1" aria-labelledby="reverseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reverseModalLabel">{{ __('Reverse Payment') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <form action="{{ route('accountant.collections.reverse', $collection) }}"
                  method="POST"
                  class="ajax"
                  data-handler="reverseShowMessage">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">
                        {{ __('You are about to reverse receipt:') }}
                        <strong>{{ $collection->receipt_number }}</strong>
                        (UGX {{ number_format($collection->amount) }})
                    </p>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Reason for Reversal') }} <span class="text-danger">*</span></label>
                        <textarea name="reverse_reason"
                                  class="form-control"
                                  rows="3"
                                  required
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

@push('script')
<script>
    window.reverseShowMessage = function (response) {
        if (response.status === 'success') {
            window.location.reload();
        } else {
            commonHandler(response);
        }
    };
</script>
@endpush
@endif
@endsection
