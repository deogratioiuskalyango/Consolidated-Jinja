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
                            <i class="ri-upload-2-line me-1"></i> {{ __('Upload Document') }}
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="documentsDataTable" class="table table-bordered table-striped align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Version') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Shareholders') }}</th>
                                        <th>{{ __('Signature') }}</th>
                                        <th>{{ __('Expiry') }}</th>
                                        <th>{{ __('Uploaded By') }}</th>
                                        <th>{{ __('Created') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documents as $index => $doc)
                                    <tr>
                                        <td>{{ $documents->firstItem() + $index }}</td>
                                        <td class="fw-medium">{{ $doc->title }}</td>
                                        <td>
                                            <span class="badge bg-secondary text-white">{{ $doc->document_type }}</span>
                                        </td>
                                        <td>{{ $doc->version ?? '1.0' }}</td>
                                        <td>
                                            @if($doc->status == DOC_STATUS_DRAFT)
                                                <span class="badge bg-secondary">{{ __('Draft') }}</span>
                                            @elseif($doc->status == DOC_STATUS_ACTIVE)
                                                <span class="badge bg-success">{{ __('Active') }}</span>
                                            @elseif($doc->status == DOC_STATUS_EXPIRED)
                                                <span class="badge bg-danger">{{ __('Expired') }}</span>
                                            @else
                                                <span class="badge bg-light text-dark">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($doc->all_shareholders)
                                                <i class="ri-check-line text-success fs-5" title="{{ __('All Shareholders') }}"></i>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($doc->requires_signature)
                                                <i class="ri-lock-line text-warning fs-5" title="{{ __('Signature Required') }}"></i>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $doc->expiry_date ? \Carbon\Carbon::parse($doc->expiry_date)->format('d M Y') : '—' }}</td>
                                        <td>{{ $doc->uploadedBy ? $doc->uploadedBy->first_name : '—' }}</td>
                                        <td>{{ $doc->created_at->format('d M Y') }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                @if($doc->file)
                                                    <a href="{{ $doc->file->file_url }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-outline-primary py-0 px-2"
                                                       title="{{ __('Download') }}">
                                                        <i class="ri-download-2-line"></i>
                                                    </a>
                                                @endif
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-success py-0 px-2 doc-share-btn"
                                                        title="{{ __('Share') }}"
                                                        data-title="{{ e($doc->title) }}"
                                                        data-type="{{ e($doc->document_type) }}"
                                                        data-desc="{{ e($doc->description) }}"
                                                        data-url="{{ $doc->file ? $doc->file->file_url : '' }}">
                                                    <i class="ri-share-forward-2-line"></i>
                                                </button>
                                                <form method="POST"
                                                      action="{{ route('admin.governance.documents.delete', $doc->id) }}"
                                                      class="d-inline ajax"
                                                      data-handler="getShowMessage">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger py-0 px-2 delete-doc-btn"
                                                            title="{{ __('Delete') }}">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $documents->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Upload Document Modal --}}
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">
                    <i class="ri-file-upload-line me-1"></i> {{ __('Upload Document') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <form action="{{ route('admin.governance.documents.store') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="ajax"
                  data-handler="getShowMessage">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">

                        {{-- Title --}}
                        <div class="col-12">
                            <label class="form-label">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="title"
                                   class="form-control"
                                   required
                                   maxlength="191"
                                   placeholder="{{ __('Enter document title') }}">
                        </div>

                        {{-- Document Type --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Document Type') }} <span class="text-danger">*</span></label>
                            <select name="document_type" class="form-select" required>
                                <option value="">{{ __('— Select Type —') }}</option>
                                <option value="Articles of Association">{{ __('Articles of Association') }}</option>
                                <option value="Board Resolution">{{ __('Board Resolution') }}</option>
                                <option value="Financial Report">{{ __('Financial Report') }}</option>
                                <option value="Meeting Minutes">{{ __('Meeting Minutes') }}</option>
                                <option value="Shareholder Agreement">{{ __('Shareholder Agreement') }}</option>
                                <option value="Policy">{{ __('Policy') }}</option>
                                <option value="Other">{{ __('Other') }}</option>
                            </select>
                        </div>

                        {{-- Version --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Version') }}</label>
                            <input type="text"
                                   name="version"
                                   class="form-control"
                                   placeholder="1.0"
                                   value="1.0">
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label class="form-label">{{ __('Description') }}</label>
                            <textarea name="description"
                                      class="form-control"
                                      rows="2"
                                      placeholder="{{ __('Optional description') }}"></textarea>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Status') }} <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="{{ DOC_STATUS_DRAFT }}">{{ __('Draft') }}</option>
                                <option value="{{ DOC_STATUS_ACTIVE }}">{{ __('Active') }}</option>
                            </select>
                        </div>

                        {{-- Expiry Date --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Expiry Date') }}</label>
                            <input type="date" name="expiry_date" class="form-control">
                        </div>

                        {{-- Checkboxes --}}
                        <div class="col-12">
                            <div class="form-check mb-2">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="all_shareholders"
                                       id="allShareholders"
                                       value="1"
                                       checked>
                                <label class="form-check-label" for="allShareholders">
                                    {{ __('Visible to all shareholders') }}
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="requires_signature"
                                       id="requiresSignature"
                                       value="1">
                                <label class="form-check-label" for="requiresSignature">
                                    <i class="ri-lock-line me-1 text-warning"></i>{{ __('Requires signature') }}
                                </label>
                            </div>
                        </div>

                        {{-- File Upload --}}
                        <div class="col-12">
                            <label class="form-label">{{ __('File') }} <span class="text-danger">*</span></label>
                            <input type="file"
                                   name="file"
                                   class="form-control"
                                   required
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <div class="form-text text-muted">
                                <i class="ri-information-line me-1"></i>
                                {{ __('Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG. Max size: 20 MB.') }}
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-upload-2-line me-1"></i>{{ __('Upload Document') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@include('components.share-modal')

@push('style')
    @include('common.layouts.datatable-style')
@endpush

@push('script')
    @include('common.layouts.datatable-script')
    <script src="{{ asset('assets/js/custom/share-utils.js') }}"></script>
    <script>
        // Share button
        $(document).on('click', '.doc-share-btn', function () {
            var title = $(this).data('title');
            var type  = $(this).data('type');
            var desc  = $(this).data('desc');
            var url   = $(this).data('url') || window.location.href;
            var text  = (type ? type + '\n' : '') + (desc || '');
            openShareModal(title, text, url);
        });

        // SweetAlert2 delete confirmation
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.delete-doc-btn').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    var form = this.closest('form');
                    Swal.fire({
                        title: '{{ __("Delete Document?") }}',
                        text: '{{ __("This action cannot be undone.") }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '{{ __("Yes, delete it") }}',
                        cancelButtonText: '{{ __("Cancel") }}'
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
