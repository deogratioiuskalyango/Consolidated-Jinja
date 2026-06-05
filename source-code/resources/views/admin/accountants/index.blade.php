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
                            <i class="ri-add-line me-1"></i> {{ __('Add Accountant') }}
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="billing-center-area bg-off-white theme-border radius-4 p-25">
                            <table id="accountantsDataTable" class="table responsive theme-border p-20">
                                <thead>
                                    <tr>
                                        <th>{{ __('SL') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Phone') }}</th>
                                        <th>{{ __('ID') }}</th>
                                        <th>{{ __('Designation') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Hidden route templates for JS --}}
                <input type="hidden" id="accEditRouteTemplate"       value="{{ route('admin.accountants.edit',       '__ID__') }}">
                <input type="hidden" id="accUpdateRouteTemplate"     value="{{ route('admin.accountants.update',     '__ID__') }}">
                <input type="hidden" id="accSuspendRouteTemplate"    value="{{ route('admin.accountants.suspend',    '__ID__') }}">
                <input type="hidden" id="accReactivateRouteTemplate" value="{{ route('admin.accountants.reactivate', '__ID__') }}">
                <input type="hidden" id="accDeleteRouteTemplate"     value="{{ route('admin.accountants.destroy',    '__ID__') }}">
            </div>
        </div>
    </div>
</div>

{{-- Add Accountant Modal --}}
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addModalLabel">{{ __('Add Accountant') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}">
                    <span class="iconify" data-icon="akar-icons:cross"></span>
                </button>
            </div>
            <form action="{{ route('admin.accountants.store') }}" method="POST" class="ajax" data-handler="accountantShowMessage">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('First Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required placeholder="{{ __('First Name') }}">
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" required placeholder="{{ __('Last Name') }}">
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Email') }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required placeholder="{{ __('Email Address') }}">
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Phone') }}</label>
                            <input type="text" name="phone" class="form-control" placeholder="{{ __('Phone Number') }}">
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Designation') }}</label>
                            <input type="text" name="designation" class="form-control" placeholder="{{ __('e.g. Senior Accountant') }}">
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('License Number') }}</label>
                            <input type="text" name="license_number" class="form-control" placeholder="{{ __('Professional licence no.') }}">
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info py-2 mb-0">
                                <i class="ri-mail-send-line me-1"></i>
                                {{ __('Login credentials will be emailed to the accountant automatically.') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-start">
                    <a href="javascript:void(0)" class="theme-btn-back me-3" data-bs-dismiss="modal">{{ __('Cancel') }}</a>
                    <button type="submit" class="theme-btn">{{ __('Add Accountant') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Accountant Modal --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">{{ __('Edit Accountant') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}">
                    <span class="iconify" data-icon="akar-icons:cross"></span>
                </button>
            </div>
            <form id="editAccountantForm" method="POST" class="ajax" data-handler="accountantEditMessage">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('First Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" id="edit_first_name" class="form-control" required placeholder="{{ __('First Name') }}">
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" id="edit_last_name" class="form-control" required placeholder="{{ __('Last Name') }}">
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Email') }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email" class="form-control" required placeholder="{{ __('Email Address') }}">
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Phone') }}</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control" placeholder="{{ __('Phone Number') }}">
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Designation') }}</label>
                            <input type="text" name="designation" id="edit_designation" class="form-control" placeholder="{{ __('e.g. Senior Accountant') }}">
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('License Number') }}</label>
                            <input type="text" name="license_number" id="edit_license_number" class="form-control" placeholder="{{ __('Professional licence no.') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-start">
                    <a href="javascript:void(0)" class="theme-btn-back me-3" data-bs-dismiss="modal">{{ __('Cancel') }}</a>
                    <button type="submit" class="theme-btn">{{ __('Save Changes') }}</button>
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
            $('#accountantsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.accountants.data') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name',         name: 'name' },
                    { data: 'email',        name: 'email' },
                    { data: 'phone',        name: 'phone',        orderable: false },
                    { data: 'accountant_id', name: 'accountant_id' },
                    { data: 'designation',  name: 'designation',  orderable: false },
                    { data: 'status_badge', name: 'status_badge', orderable: false },
                    { data: 'action',       name: 'action',       orderable: false, searchable: false },
                ],
            });
        });

        // Edit
        $(document).on('click', '.acc-edit-btn', function () {
            var id  = $(this).data('id');
            var url = $('#accEditRouteTemplate').val().replace('__ID__', id);
            $.getJSON(url, function (res) {
                if (res.status !== 200) { toastr.error('Could not load accountant data.'); return; }
                var d = res.data;
                $('#edit_first_name').val(d.first_name);
                $('#edit_last_name').val(d.last_name);
                $('#edit_email').val(d.email);
                $('#edit_phone').val(d.phone);
                $('#edit_designation').val(d.designation);
                $('#edit_license_number').val(d.license_number);
                $('#editAccountantForm').attr('action', $('#accUpdateRouteTemplate').val().replace('__ID__', id));
                $('#editModal').modal('show');
            });
        });

        // Suspend
        $(document).on('click', '.acc-suspend-btn', function () {
            var url = $('#accSuspendRouteTemplate').val().replace('__ID__', $(this).data('id'));
            if (!confirm('{{ __('Suspend this accountant?') }}')) return;
            $.post(url, { _token: $('meta[name="csrf-token"]').attr('content') }, function (res) {
                if (res && res.status === true) { toastr.success(res.message); setTimeout(function () { location.reload(); }, 600); }
                else commonHandler(res);
            });
        });

        // Reactivate
        $(document).on('click', '.acc-reactivate-btn', function () {
            var url = $('#accReactivateRouteTemplate').val().replace('__ID__', $(this).data('id'));
            if (!confirm('{{ __('Reactivate this accountant?') }}')) return;
            $.post(url, { _token: $('meta[name="csrf-token"]').attr('content') }, function (res) {
                if (res && res.status === true) { toastr.success(res.message); setTimeout(function () { location.reload(); }, 600); }
                else commonHandler(res);
            });
        });

        // Delete
        $(document).on('click', '.acc-delete-btn', function () {
            var url = $('#accDeleteRouteTemplate').val().replace('__ID__', $(this).data('id'));
            if (!confirm('{{ __('Delete this accountant? This cannot be undone.') }}')) return;
            $.ajax({
                url: url,
                method: 'DELETE',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    if (res && res.status === true) { toastr.success(res.message); setTimeout(function () { location.reload(); }, 600); }
                    else commonHandler(res);
                },
                error: function (xhr) { commonHandler(xhr); }
            });
        });

        window.accountantShowMessage = function (response) {
            if (response && response.status === true) {
                toastr.success(response.message || '{{ __('Accountant created.') }}');
                $('#addModal').modal('hide');
                $('#addModal form')[0].reset();
                setTimeout(function () { location.reload(); }, 800);
            } else {
                commonHandler(response);
            }
        };

        window.accountantEditMessage = function (response) {
            if (response && response.status === true) {
                toastr.success(response.message || '{{ __('Updated.') }}');
                $('#editModal').modal('hide');
                setTimeout(function () { location.reload(); }, 800);
            } else {
                commonHandler(response);
            }
        };
    </script>
@endpush
