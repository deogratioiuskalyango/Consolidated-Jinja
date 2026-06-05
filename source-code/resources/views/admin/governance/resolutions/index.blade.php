@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between border-bottom mb-20">
                            <div class="page-title-left">
                                <h3 class="mb-sm-0">{{ $pageTitle }}</h3>
                            </div>
                            <div class="page-title-right">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                    <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="ri-add-line me-1"></i> {{ __('Create Resolution') }}
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="resolutionsDataTable" class="table table-bordered table-striped align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('SL') }}</th>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Voting Opens') }}</th>
                                        <th>{{ __('Voting Closes') }}</th>
                                        <th>{{ __('Quorum %') }}</th>
                                        <th>{{ __('For %') }}</th>
                                        <th>{{ __('Against %') }}</th>
                                        <th>{{ __('Created') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $typeLabels = [
                                            1 => 'Financial', 2 => 'Board', 3 => 'Acquisition',
                                            4 => 'Dividend', 5 => 'Share Transfer', 6 => 'Policy',
                                            7 => 'Director', 8 => 'Other',
                                        ];
                                    @endphp
                                    @foreach($resolutions as $index => $resolution)
                                    <tr>
                                        <td>{{ $resolutions->firstItem() + $index }}</td>
                                        <td>{{ $resolution->title }}</td>
                                        <td>{{ $typeLabels[$resolution->type] ?? '—' }}</td>
                                        <td>
                                            @if($resolution->status == RESOLUTION_STATUS_DRAFT)
                                                <span class="badge bg-secondary">{{ __('Draft') }}</span>
                                            @elseif($resolution->status == RESOLUTION_STATUS_OPEN)
                                                <span class="badge bg-primary">{{ __('Open') }}</span>
                                            @elseif($resolution->status == RESOLUTION_STATUS_CLOSED)
                                                <span class="badge bg-dark">{{ __('Closed') }}</span>
                                            @elseif($resolution->status == RESOLUTION_STATUS_PASSED)
                                                <span class="badge bg-success">{{ __('Passed') }}</span>
                                            @elseif($resolution->status == RESOLUTION_STATUS_FAILED)
                                                <span class="badge bg-danger">{{ __('Failed') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $resolution->voting_opens_at ? \Carbon\Carbon::parse($resolution->voting_opens_at)->format('d M Y H:i') : '—' }}</td>
                                        <td>{{ $resolution->voting_closes_at ? \Carbon\Carbon::parse($resolution->voting_closes_at)->format('d M Y H:i') : '—' }}</td>
                                        <td>{{ $resolution->quorum_percentage ?? '—' }}%</td>
                                        <td>{{ $resolution->votes_for_percentage ?? '0' }}%</td>
                                        <td>{{ $resolution->votes_against_percentage ?? '0' }}%</td>
                                        <td>{{ $resolution->created_at->format('d M Y') }}</td>
                                        <td>
                                            <div class="d-inline-flex gap-1">
                                                @if(!$resolution->votes()->exists())
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-primary res-edit-btn"
                                                            data-id="{{ $resolution->id }}"
                                                            data-url="{{ route('admin.governance.resolutions.edit', $resolution) }}"
                                                            title="{{ __('Edit') }}">
                                                        <i class="ri-pencil-line"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger res-delete-btn"
                                                            data-id="{{ $resolution->id }}"
                                                            data-title="{{ $resolution->title }}"
                                                            data-url="{{ route('admin.governance.resolutions.destroy', $resolution) }}"
                                                            title="{{ __('Delete') }}">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                @else
                                                    <span class="badge bg-light text-muted border">{{ __('Has votes') }}</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $resolutions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Create Resolution Modal --}}
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">{{ __('Create Resolution') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <form action="{{ route('admin.governance.resolutions.store') }}" method="POST" class="ajax" data-handler="resolutionShowMessage">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required placeholder="{{ __('Enter resolution title') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('Description') }} <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="4" required placeholder="{{ __('Enter resolution description') }}"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Resolution Type') }} <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="">{{ __('Select Type') }}</option>
                                <option value="1">{{ __('Financial') }}</option>
                                <option value="2">{{ __('Board') }}</option>
                                <option value="3">{{ __('Acquisition') }}</option>
                                <option value="4">{{ __('Dividend') }}</option>
                                <option value="5">{{ __('Share Transfer') }}</option>
                                <option value="6">{{ __('Policy') }}</option>
                                <option value="7">{{ __('Director') }}</option>
                                <option value="8">{{ __('Other') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Quorum Percentage (%)') }}</label>
                            <input type="number" name="quorum_percentage" class="form-control" min="0" max="100" step="0.01" value="51" placeholder="51">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Voting Opens At') }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="voting_opens_at" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Voting Closes At') }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="voting_closes_at" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Pass Threshold (%)') }}</label>
                            <input type="number" name="pass_threshold" class="form-control" min="0" max="100" step="0.01" value="51" placeholder="51">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Options') }}</label>
                            <div class="d-flex gap-4 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="weighted_voting" id="weighted_voting" value="1" checked>
                                    <label class="form-check-label" for="weighted_voting">{{ __('Weighted Voting') }}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="anonymous_voting" id="anonymous_voting" value="1">
                                    <label class="form-check-label" for="anonymous_voting">{{ __('Anonymous') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Create Resolution') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- Hidden route templates --}}
<input type="hidden" id="resEditRouteTemplate"   value="{{ route('admin.governance.resolutions.edit',    '__ID__') }}">
<input type="hidden" id="resUpdateRouteTemplate" value="{{ route('admin.governance.resolutions.update',  '__ID__') }}">
<input type="hidden" id="resDeleteRouteTemplate" value="{{ route('admin.governance.resolutions.destroy', '__ID__') }}">

{{-- Edit Resolution Modal --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">{{ __('Edit Resolution') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editResolutionForm" method="POST" class="ajax" data-handler="resolutionEditMessage">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="edit_res_title" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('Description') }} <span class="text-danger">*</span></label>
                            <textarea name="description" id="edit_res_description" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Resolution Type') }} <span class="text-danger">*</span></label>
                            <select name="type" id="edit_res_type" class="form-select" required>
                                <option value="1">{{ __('Financial') }}</option>
                                <option value="2">{{ __('Board') }}</option>
                                <option value="3">{{ __('Acquisition') }}</option>
                                <option value="4">{{ __('Dividend') }}</option>
                                <option value="5">{{ __('Share Transfer') }}</option>
                                <option value="6">{{ __('Policy') }}</option>
                                <option value="7">{{ __('Director') }}</option>
                                <option value="8">{{ __('Other') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Quorum Percentage (%)') }}</label>
                            <input type="number" name="quorum_percentage" id="edit_res_quorum" class="form-control" min="0" max="100" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Voting Opens At') }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="voting_opens_at" id="edit_res_opens" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Voting Closes At') }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="voting_closes_at" id="edit_res_closes" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Pass Threshold (%)') }}</label>
                            <input type="number" name="pass_threshold" id="edit_res_threshold" class="form-control" min="0" max="100" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Options') }}</label>
                            <div class="d-flex gap-4 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="weighted_voting" id="edit_weighted_voting" value="1">
                                    <label class="form-check-label" for="edit_weighted_voting">{{ __('Weighted Voting') }}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="anonymous_voting" id="edit_anonymous_voting" value="1">
                                    <label class="form-check-label" for="edit_anonymous_voting">{{ __('Anonymous') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('style')
    @include('common.layouts.datatable-style')
@endpush

@push('script')
    @include('common.layouts.datatable-script')
    <script src="{{ asset('assets/js/custom/governance-resolutions.js') }}"></script>
@endpush
