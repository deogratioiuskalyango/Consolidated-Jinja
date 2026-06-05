/* Tenant Maintenance Request — DataTable init */
(function ($) {
    "use strict";

    var oTable = $('#allMaintenanceRequestDataTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        responsive: true,
        ajax: $('#maintenanceIndexRoute').val(),
        order: [[0, 'desc']],
        ordering: false,
        autoWidth: false,
        drawCallback: function () {
            $(".dataTables_length select").addClass("form-select form-select-sm");
        },
        language: {
            paginate: {
                previous: '<span class="iconify" data-icon="icons8:angle-left"></span>',
                next:     '<span class="iconify" data-icon="icons8:angle-right"></span>'
            },
            emptyTable: 'No maintenance requests yet. Click <strong>New Request</strong> to create one.',
        },
        columns: [
            { data: 'request_id',  name: 'request_id' },
            { data: 'issue_name',  name: 'maintenance_issues.name' },
            { data: 'details' },
            { data: 'status' },
            { data: 'action', className: 'text-end', orderable: false },
        ]
    });

    /* Reload table after successful form submission */
    $(document).on('mwizSuccess', function () {
        oTable.ajax.reload();
    });

})(jQuery);
