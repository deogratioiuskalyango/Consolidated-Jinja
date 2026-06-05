'use strict';

$(function () {

    // ── DataTable ──────────────────────────────────────────────────────────────
    if ($('#collectionsTable').length) {
        $('#collectionsTable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[7, 'desc']],
            columnDefs: [
                { orderable: false, targets: [-1] }
            ]
        });
    }

    // ── Reverse Payment Handler ────────────────────────────────────────────────
    $(document).on('click', '.collection-reverse-btn', function () {
        var url     = $(this).data('url');
        var receipt = $(this).data('receipt') || 'this payment';

        if (!confirm('Are you sure you want to reverse receipt: ' + receipt + '? This action cannot be undone.')) {
            return;
        }

        $.ajax({
            url:    url,
            method: 'POST',
            data: {
                _token:  $('meta[name="csrf-token"]').attr('content'),
                _method: 'PATCH'
            },
            success: function (response) {
                if (response && response.status === true) {
                    toastr.success(response.message || 'Payment reversed successfully.');
                    setTimeout(function () {
                        window.location.href = '/accountant/collections';
                    }, 800);
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

    // ── AJAX Form Callback ─────────────────────────────────────────────────────
    window.collectionShowMessage = function (response) {
        if (response && response.status === true) {
            toastr.success(response.message || 'Operation completed successfully.');
            setTimeout(function () {
                window.location.href = '/accountant/collections';
            }, 800);
        } else {
            commonHandler(response);
        }
    };

});
