@extends('shareholder.layouts.app')
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-flex flex-column flex-sm-row align-items-sm-center justify-content-between g-20">
                            <div class="page-title-left">
                                <h2 class="mb-sm-0">{{ __('Share Transfers') }}</h2>
                            </div>
                            <div>
                                @if(!isset($canTransfer) || $canTransfer)
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#transferModal">
                                        <i class="ri-exchange-line me-1"></i>{{ __('Request Transfer') }}
                                    </button>
                                @else
                                    <button type="button" class="btn btn-secondary btn-sm" disabled title="{{ __('Your share class does not permit transfers') }}">
                                        <i class="ri-forbid-line me-1"></i>{{ __('Transfers Restricted') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Transfer restriction notices --}}
                @if(isset($canTransfer) && $canTransfer)
                    @if(!empty($needsBoard))
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-warning py-2 mb-0 small">
                                <i class="ri-group-line me-1"></i>
                                {{ __('Your transfers require board approval before they are processed.') }}
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(!empty($needsCompliance))
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-info py-2 mb-0 small">
                                <i class="ri-file-search-line me-1"></i>
                                {{ __('Your transfers require a compliance review before they are processed.') }}
                            </div>
                        </div>
                    </div>
                    @endif
                @elseif(isset($canTransfer) && !$canTransfer)
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="alert alert-danger py-2 mb-0 small">
                            <i class="ri-forbid-line me-1"></i>
                            {{ __('Your share class (') }}{{ $shareholder->shareClass->name ?? '' }}{{ __(') does not permit share transfers.') }}
                        </div>
                    </div>
                </div>
                @endif

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Type') }}</th>
                                                <th>{{ __('Other Party') }}</th>
                                                <th>{{ __('Share Class') }}</th>
                                                <th>{{ __('Quantity') }}</th>
                                                <th>{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($transfers as $transfer)
                                                @php
                                                    $statusMap = [
                                                        0 => ['label' => 'Pending',  'class' => 'bg-warning text-dark'],
                                                        1 => ['label' => 'Approved', 'class' => 'bg-success'],
                                                        2 => ['label' => 'Rejected', 'class' => 'bg-danger'],
                                                    ];
                                                    $st = $statusMap[$transfer->status] ?? ['label' => 'Unknown', 'class' => 'bg-secondary'];

                                                    $isOutgoing = $transfer->from_shareholder_id == $shareholder->id;
                                                    $typeLabel  = $isOutgoing ? __('Outgoing') : __('Incoming');
                                                    $typeClass  = $isOutgoing ? 'bg-warning text-dark' : 'bg-info';

                                                    $otherParty = $isOutgoing
                                                        ? ($transfer->toShareholder?->user?->name ?? $transfer->to_shareholder_name ?? '-')
                                                        : ($transfer->fromShareholder?->user?->name ?? '-');
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $transfer->created_at->format('M d, Y') }}</td>
                                                    <td><span class="badge {{ $typeClass }}">{{ $typeLabel }}</span></td>
                                                    <td>{{ $otherParty }}</td>
                                                    <td>{{ $transfer->fromShareholder?->shareClass?->name ?? '-' }}</td>
                                                    <td>{{ number_format($transfer->shares, 4) }}</td>
                                                    <td><span class="badge {{ $st['class'] }}">{{ __($st['label']) }}</span></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-4">
                                                        <i class="ri-exchange-line fs-2 d-block mb-2"></i>
                                                        {{ __('No share transfers found.') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            {{ $transfers->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Transfer Request Modal (only rendered when transfers are permitted) --}}
@if(!isset($canTransfer) || $canTransfer)
<div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferModalLabel">
                    <i class="ri-exchange-line me-2"></i>{{ __('Request Share Transfer') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('shareholder.share-transfers.store') }}" method="POST">
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <label for="to_shareholder_id" class="form-label fw-semibold">{{ __('Transfer To') }} <span class="text-danger">*</span></label>
                        <select class="form-select @error('to_shareholder_id') is-invalid @enderror"
                                id="to_shareholder_id" name="to_shareholder_id" required>
                            <option value="">{{ __('-- Select Shareholder --') }}</option>
                            @foreach($shareholders as $sh)
                                @if($sh->id != $shareholder->id)
                                    <option value="{{ $sh->id }}" {{ old('to_shareholder_id') == $sh->id ? 'selected' : '' }}>
                                        {{ $sh->user->name ?? $sh->full_name ?? 'Shareholder #' . $sh->id }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('to_shareholder_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">
                            <i class="ri-information-line me-1"></i>
                            {{ __('Your share class:') }}
                            <strong>{{ $shareholder->shareClass?->name ?? '-' }}</strong>
                        </label>
                    </div>

                    <div class="mb-3">
                        <label for="shares" class="form-label fw-semibold">{{ __('Number of Shares') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.0001" min="0.0001"
                               class="form-control @error('shares') is-invalid @enderror"
                               id="shares" name="shares" value="{{ old('shares') }}"
                               placeholder="{{ __('Shares to transfer (max: ') . number_format($shareholder->total_shares, 4) . ')' }}"
                               required>
                        <div class="form-text">{{ __('You hold') }} {{ number_format($shareholder->total_shares, 4) }} {{ __('shares.') }}</div>
                        @error('shares')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="price_per_share" class="form-label fw-semibold">{{ __('Price Per Share') }} <span class="text-muted small">({{ __('optional') }})</span></label>
                        <input type="number" step="0.0001" min="0"
                               class="form-control @error('price_per_share') is-invalid @enderror"
                               id="price_per_share" name="price_per_share" value="{{ old('price_per_share') }}"
                               placeholder="0.00">
                        @error('price_per_share')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label fw-semibold">{{ __('Reason') }} <span class="text-muted small">({{ __('optional') }})</span></label>
                        <textarea class="form-control @error('reason') is-invalid @enderror"
                                  id="reason" name="reason" rows="3"
                                  placeholder="{{ __('Reason for transfer...') }}">{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-send-plane-line me-1"></i>{{ __('Submit Request') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif {{-- end canTransfer modal wrapper --}}
@endsection

@push('script')
<script>
    // Re-open modal if validation errors
    @if($errors->any() && (!isset($canTransfer) || $canTransfer))
        var modal = new bootstrap.Modal(document.getElementById('transferModal'));
        modal.show();
    @endif
</script>
@endpush
