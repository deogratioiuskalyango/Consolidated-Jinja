'use strict';

// ─── Mode selector ────────────────────────────────────────────────────────────

(function () {
    var modeCards    = document.querySelectorAll('.mode-card');
    var modeInput    = document.getElementById('modeInput');
    var venueSection  = document.getElementById('venueSection');
    var virtualSection= document.getElementById('virtualSection');

    if (!modeCards.length || !modeInput) return;

    function updateMode(mode) {
        modeInput.value = mode;
        modeCards.forEach(function (card) {
            card.classList.toggle('active', parseInt(card.dataset.mode) === parseInt(mode));
        });

        var isVirtual = (parseInt(mode) === 2); // MEETING_MODE_VIRTUAL
        var isHybrid  = (parseInt(mode) === 3); // MEETING_MODE_HYBRID

        if (venueSection) {
            venueSection.classList.toggle('d-none', isVirtual);
        }
        if (virtualSection) {
            virtualSection.classList.toggle('d-none', !isVirtual && !isHybrid);
        }
    }

    modeCards.forEach(function (card) {
        card.addEventListener('click', function () {
            updateMode(parseInt(this.dataset.mode));
        });
    });

    // Initialise with default
    updateMode(parseInt(modeInput.value) || 1);
})();

// ─── DataTable initialisation ─────────────────────────────────────────────────

$(document).ready(function () {
    if ($('#meetingsDataTable').length) {
        $('#meetingsDataTable').DataTable({
            responsive: true,
            order: [[4, 'desc']], // Sort by scheduled date desc
            columnDefs: [
                { orderable: false, targets: [8] }, // Actions
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search meetings...',
            },
        });
    }
});

// ─── After-store handler — show the generated Meet link ──────────────────────

window.meetingStoreHandler = function (res) {
    if (res.status) {
        var msg = res.message || 'Meeting scheduled successfully.';
        toastr.success(msg);

        if (res.data && res.data.virtual_link) {
            // Show the Meet link in a toast so admin can copy it immediately
            toastr.info(
                '<a href="' + res.data.virtual_link + '" target="_blank">' +
                res.data.virtual_link + '</a>',
                'Google Meet Link',
                { timeOut: 10000, extendedTimeOut: 5000, allowHtml: true }
            );
        }

        // Close modal and reload
        var modal = bootstrap.Modal.getInstance(document.getElementById('addModal'));
        if (modal) modal.hide();

        setTimeout(function () { location.reload(); }, 1200);
    } else {
        toastr.error(res.message || 'Failed to schedule meeting.');
    }
};
