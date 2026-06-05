'use strict';

$(function () {

    // ── DataTable ──────────────────────────────────────────────────────────────
    if ($('#resolutionsDataTable').length) {
        $('#resolutionsDataTable').DataTable({
            responsive: true,
            order: [[9, 'desc']],
        });
    }

    var editRouteTemplate   = $('#resEditRouteTemplate').val();
    var updateRouteTemplate = $('#resUpdateRouteTemplate').val();
    var deleteRouteTemplate = $('#resDeleteRouteTemplate').val();

    // ── Edit ───────────────────────────────────────────────────────────────────
    $(document).on('click', '.res-edit-btn', function () {
        var id  = $(this).data('id');
        var url = editRouteTemplate.replace('__ID__', id);

        $.getJSON(url, function (res) {
            if (res.status !== 200) { toastr.error('Could not load resolution.'); return; }
            var d = res.data;
            $('#edit_res_title').val(d.title);
            $('#edit_res_description').val(d.description);
            $('#edit_res_type').val(d.type);
            $('#edit_res_opens').val(d.voting_opens_at);
            $('#edit_res_closes').val(d.voting_closes_at);
            $('#edit_res_quorum').val(d.quorum_percentage);
            $('#edit_res_threshold').val(d.pass_threshold);
            $('#edit_weighted_voting').prop('checked', !!d.weighted_voting);
            $('#edit_anonymous_voting').prop('checked', !!d.anonymous_voting);
            $('#editResolutionForm').attr('action', updateRouteTemplate.replace('__ID__', id));
            $('#editModal').modal('show');
        }).fail(function () { toastr.error('Request failed.'); });
    });

    // ── Delete ─────────────────────────────────────────────────────────────────
    $(document).on('click', '.res-delete-btn', function () {
        var id    = $(this).data('id');
        var title = $(this).data('title') || 'this resolution';
        var url   = deleteRouteTemplate.replace('__ID__', id);
        if (!confirm('Permanently delete "' + title + '"? This cannot be undone.')) return;

        $.ajax({
            url:    url,
            method: 'DELETE',
            data:   { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                if (res && res.status === true) {
                    toastr.success(res.message || 'Resolution deleted.');
                    setTimeout(function () { location.reload(); }, 600);
                } else {
                    commonHandler(res);
                }
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Request failed.';
                toastr.error(msg);
            }
        });
    });

    // ── Add form callback ──────────────────────────────────────────────────────
    window.resolutionShowMessage = function (response) {
        if (response && response.status === true) {
            toastr.success(response.message || 'Resolution created successfully.');
            $('#addModal').modal('hide');
            $('#addModal form')[0].reset();
            setTimeout(function () { location.reload(); }, 800);
        } else {
            commonHandler(response);
        }
    };

    // ── Edit form callback ─────────────────────────────────────────────────────
    window.resolutionEditMessage = function (response) {
        if (response && response.status === true) {
            toastr.success(response.message || 'Resolution updated successfully.');
            $('#editModal').modal('hide');
            setTimeout(function () { location.reload(); }, 800);
        } else {
            commonHandler(response);
        }
    };

});
