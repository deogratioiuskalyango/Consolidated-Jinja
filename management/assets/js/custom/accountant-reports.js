'use strict';

$(function () {

    // ── DataTable ──────────────────────────────────────────────────────────────
    if ($('#reportsTable').length) {
        $('#reportsTable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[7, 'desc']],
            columnDefs: [
                { orderable: false, targets: [-1] }
            ]
        });
    }

    // ── Share Report Handler ───────────────────────────────────────────────────
    $(document).on('click', '.report-share-btn', function () {
        var url = $(this).data('url');

        $.ajax({
            url:    url,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response && response.status === true) {
                    toastr.success(response.message || 'Report shared with shareholders successfully.');
                    setTimeout(function () { location.reload(); }, 800);
                } else {
                    commonHandler(response);
                }
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Request failed. Please try again.';
                toastr.error(msg);
            }
        });
    });

    // ── Delete Report Handler ──────────────────────────────────────────────────
    $(document).on('click', '.report-delete-btn', function () {
        var url = $(this).data('url');

        if (!confirm('Are you sure you want to delete this report? This cannot be undone.')) {
            return;
        }

        $.ajax({
            url:    url,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response && response.status === true) {
                    toastr.success(response.message || 'Report deleted successfully.');
                    setTimeout(function () { location.reload(); }, 800);
                } else {
                    commonHandler(response);
                }
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Request failed. Please try again.';
                toastr.error(msg);
            }
        });
    });

    // ── Generate Report Form Callback ──────────────────────────────────────────
    window.reportShowMessage = function (response) {
        if (response && response.status === true) {
            toastr.success(response.message || 'Report generated successfully.');
            $('#generateModal').modal('hide');
            $('#generateModal form')[0].reset();
            setTimeout(function () { location.reload(); }, 800);
        } else {
            commonHandler(response);
        }
    };

});
