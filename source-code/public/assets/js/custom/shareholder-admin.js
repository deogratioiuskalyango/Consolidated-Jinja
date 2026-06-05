'use strict';

$(function () {

    // ── DataTable ──────────────────────────────────────────────────────────────
    var dataRoute = $('#shareholderDataRoute').val();

    if (dataRoute) {
        $('#shareholderDataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: dataRoute,
                type: 'GET',
                error: function (xhr) {
                    if (xhr.status === 401 || xhr.status === 419) {
                        toastr.error('Session expired. Redirecting to login…');
                        setTimeout(function () { window.location.href = '/kintu/login'; }, 1500);
                    } else {
                        toastr.error('Failed to load shareholders. Please refresh the page.');
                    }
                },
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '40px' },
                { data: 'name',         name: 'name' },
                { data: 'email',        name: 'users.email' },
                { data: 'phone',        name: 'users.contact_number', orderable: false },
                { data: 'share_class',  name: 'share_classes.name', orderable: false },
                { data: 'shares',       name: 'total_shares', width: '110px' },
                { data: 'percentage',   name: 'ownership_percentage', width: '110px' },
                { data: 'roles',        name: 'roles', orderable: false, searchable: false },
                { data: 'status_badge', name: 'status', orderable: false, width: '90px' },
                { data: 'action',       name: 'action', orderable: false, searchable: false, width: '120px' },
            ],
            order: [[1, 'asc']],
            autoWidth: false,
            scrollX: true,
            scrollCollapse: true,
        });
    }

    // ── Edit (load data into modal) ────────────────────────────────────────────
    var editRouteTemplate   = $('#shareholderEditRoute').val();
    var updateRouteTemplate = $('#shareholderUpdateRoute').val();

    $(document).on('click', '.sh-edit-btn', function () {
        var id  = $(this).data('id');
        var url = editRouteTemplate.replace('__ID__', id);

        $.getJSON(url, function (res) {
            if (res.status !== 200) { toastr.error('Could not load shareholder data.'); return; }
            var d = res.data;
            $('#edit_first_name').val(d.first_name);
            $('#edit_last_name').val(d.last_name);
            $('#edit_email').val(d.email);
            $('#edit_phone').val(d.phone);
            $('#edit_share_class_id').val(d.share_class_id);
            $('#edit_total_shares').val(d.total_shares);
            $('#edit_fixed_voting_weight').val(d.fixed_voting_weight);
            $('#editShareholderForm').attr('action', updateRouteTemplate.replace('__ID__', id));
            $('#editModal').modal('show');
        }).fail(function () { toastr.error('Request failed.'); });
    });

    // ── Delete ─────────────────────────────────────────────────────────────────
    var deleteRouteTemplate = $('#shareholderDeleteRoute').val();

    $(document).on('click', '.sh-delete-btn', function () {
        var id   = $(this).data('id');
        var name = $(this).data('name') || 'this shareholder';
        if (!confirm('Permanently delete ' + name + '? This cannot be undone.')) return;

        $.ajax({
            url:    deleteRouteTemplate.replace('__ID__', id),
            method: 'DELETE',
            data:   { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                if (res && res.status === true) {
                    toastr.success(res.message || 'Shareholder deleted.');
                    $('#shareholderDataTable').DataTable().ajax.reload(null, false);
                } else {
                    toastr.error(res.message || 'An error occurred.');
                }
            },
            error: function () { toastr.error('Request failed.'); }
        });
    });

    // ── Suspend ────────────────────────────────────────────────────────────────
    var suspendRouteTemplate    = $('#shareholderSuspendRoute').val();
    var reactivateRouteTemplate = $('#shareholderReactivateRoute').val();

    $(document).on('click', '.sh-suspend-btn', function () {
        var id = $(this).data('id');
        if (!confirm('Suspend this shareholder?')) return;

        $.ajax({
            url:     suspendRouteTemplate.replace('__ID__', id),
            method:  'POST',
            data:    { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                if (res && res.status === true) {
                    toastr.success(res.message || 'Shareholder suspended.');
                    $('#shareholderDataTable').DataTable().ajax.reload(null, false);
                } else {
                    toastr.error(res.message || 'An error occurred.');
                }
            },
            error: function () { toastr.error('Request failed.'); }
        });
    });

    // ── Reactivate ─────────────────────────────────────────────────────────────
    $(document).on('click', '.sh-reactivate-btn', function () {
        var id = $(this).data('id');
        if (!confirm('Reactivate this shareholder?')) return;

        $.ajax({
            url:     reactivateRouteTemplate.replace('__ID__', id),
            method:  'POST',
            data:    { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                if (res && res.status === true) {
                    toastr.success(res.message || 'Shareholder reactivated.');
                    $('#shareholderDataTable').DataTable().ajax.reload(null, false);
                } else {
                    toastr.error(res.message || 'An error occurred.');
                }
            },
            error: function () { toastr.error('Request failed.'); }
        });
    });

    // ── Resend Credentials ────────────────────────────────────────────────────
    var resendRouteTemplate = $('#shareholderResendRoute').val();

    $(document).on('click', '.sh-resend-btn', function () {
        var id   = $(this).data('id');
        var name = $(this).data('name') || 'this shareholder';
        if (!confirm('Reset password and resend login credentials to ' + name + '?')) return;

        var btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url:    resendRouteTemplate.replace('__ID__', id),
            method: 'POST',
            data:   { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                btn.prop('disabled', false);
                if (res && res.status === true) {
                    toastr.success(res.message || 'Credentials sent.');
                } else {
                    toastr.error(res.message || 'An error occurred.');
                }
            },
            error: function () {
                btn.prop('disabled', false);
                toastr.error('Request failed.');
            }
        });
    });

    // Shareholder roles
    var rolesRouteTemplate = $('#shareholderRolesRoute').val();
    var rolesUpdateRouteTemplate = $('#shareholderRolesUpdateRoute').val();

    function resetRolesModal() {
        var form = $('#rolesShareholderForm');
        if (form.length && form[0]) {
            form[0].reset();
        }
        $('.shareholder-role-input').prop('checked', false);
        $('#roles_notes').val('');
        $('#rolesShareholderSubtitle').text('Assign additional system roles to this shareholder.');
    }

    $(document).on('click', '.sh-roles-btn', function () {
        var id = $(this).data('id');
        var url = rolesRouteTemplate.replace('__ID__', id);

        resetRolesModal();
        $('#rolesShareholderForm').attr('action', rolesUpdateRouteTemplate.replace('__ID__', id));

        $.getJSON(url, function (res) {
            if (res.status !== 200) {
                toastr.error('Could not load shareholder roles.');
                return;
            }

            var data = res.data || {};
            $('#rolesShareholderSubtitle').text((data.name || 'Shareholder') + (data.email ? ' - ' + data.email : ''));

            (data.assigned_roles || []).forEach(function (slug) {
                $('#role_' + slug).prop('checked', true);
            });

            $('#rolesModal .shareholder-roles-body').scrollTop(0);

            $('#rolesModal').modal('show');
        }).fail(function () {
            toastr.error('Request failed.');
        });
    });

    $('#rolesShareholderForm').on('submit', function (e) {
        e.preventDefault();

        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true);

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function (res) {
                submitBtn.prop('disabled', false);
                if (res && res.status === true) {
                    toastr.success(res.message || 'Shareholder roles updated.');
                    $('#rolesModal').modal('hide');
                    $('#shareholderDataTable').DataTable().ajax.reload(null, false);
                } else {
                    toastr.error(res.message || 'An error occurred.');
                }
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false);
                var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Request failed.';
                toastr.error(message);
            }
        });
    });
    window.getShowMessage = function (response) {
        if (response && response.status === true) {
            toastr.success(response.message || 'Shareholder added successfully.');
            $('#addModal').modal('hide');
            $('#addModal form')[0].reset();
            $('#shareholderDataTable').DataTable().ajax.reload(null, false);
        } else {
            commonHandler(response);
        }
    };

    // ── Edit form success handler ──────────────────────────────────────────────
    window.getEditMessage = function (response) {
        if (response && response.status === true) {
            toastr.success(response.message || 'Shareholder updated successfully.');
            $('#editModal').modal('hide');
            $('#shareholderDataTable').DataTable().ajax.reload(null, false);
        } else {
            commonHandler(response);
        }
    };

});
