'use strict';

$(function () {

    // ── DataTable ──────────────────────────────────────────────────────────────
    if ($('#reconTable').length) {
        $('#reconTable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[4, 'desc']],
            columnDefs: [
                { orderable: false, targets: [-1] }
            ]
        });
    }

    // ── Match Button: open match modal with correct action ─────────────────────
    $(document).on('click', '.recon-match-btn', function () {
        var url = $(this).data('url');
        $('#matchForm').attr('action', url);
        $('#matchForm input[name="collection_id"]').val('');
        $('#matchModal').modal('show');
    });

    // ── Flag Button: open flag modal with correct action ───────────────────────
    $(document).on('click', '.recon-flag-btn', function () {
        var url = $(this).data('url');
        $('#flagForm').attr('action', url);
        $('#flagForm select[name="status"]').val('');
        $('#flagForm textarea[name="flag_reason"]').val('');
        $('#flagModal').modal('show');
    });

    // ── Upload Transaction Form Callback ───────────────────────────────────────
    window.reconShowMessage = function (response) {
        if (response && response.status === true) {
            toastr.success(response.message || 'Transaction uploaded successfully.');
            $('#uploadModal').modal('hide');
            $('#uploadModal form')[0].reset();
            setTimeout(function () { location.reload(); }, 800);
        } else {
            commonHandler(response);
        }
    };

    // ── Match Form Callback ────────────────────────────────────────────────────
    window.matchShowMessage = function (response) {
        if (response && response.status === true) {
            toastr.success(response.message || 'Transaction matched successfully.');
            $('#matchModal').modal('hide');
            setTimeout(function () { location.reload(); }, 800);
        } else {
            commonHandler(response);
        }
    };

    // ── Flag Form Callback ─────────────────────────────────────────────────────
    window.flagShowMessage = function (response) {
        if (response && response.status === true) {
            toastr.success(response.message || 'Transaction flagged successfully.');
            $('#flagModal').modal('hide');
            setTimeout(function () { location.reload(); }, 800);
        } else {
            commonHandler(response);
        }
    };

});
