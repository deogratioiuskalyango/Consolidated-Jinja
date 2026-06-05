/**
 * Zaiproty Share Utility
 *
 * openShareModal(title, text, url)
 *   title – heading shown in the modal and used as email subject
 *   text  – body copy (notice details, meeting agenda, etc.)
 *   url   – canonical link (falls back to current page)
 */
window.openShareModal = function (title, text, url) {
    url   = url   || window.location.href;
    title = title || '';
    text  = text  || '';

    $('#shareModalHeading').text(title || 'Share');
    $('#sharePreviewText').text(text || title);
    $('#shareCopyUrl').val(url);

    // stash for platform handlers
    $('#shareModal')
        .data('shareTitle', title)
        .data('shareText',  text)
        .data('shareUrl',   url);

    // reset copy button label in case it was changed before
    $('#shareCopyBtn').html('<i class="ri-clipboard-line me-1"></i>Copy');

    var modal = new bootstrap.Modal(document.getElementById('shareModal'));
    modal.show();
};

// ── Platform click handler ────────────────────────────────────────────────────
$(document).on('click', '.share-platform-btn', function () {
    var $modal   = $('#shareModal');
    var title    = $modal.data('shareTitle') || '';
    var text     = $modal.data('shareText')  || '';
    var url      = $modal.data('shareUrl')   || window.location.href;
    var platform = $(this).data('platform');

    var fullMessage = title ? (title + '\n\n' + text + '\n\n' + url) : (text + '\n\n' + url);

    var encoded    = encodeURIComponent(fullMessage);
    var encUrl     = encodeURIComponent(url);
    var encText    = encodeURIComponent(title ? title + '\n\n' + text : text);
    var encTitle   = encodeURIComponent(title);

    var shareUrl;

    switch (platform) {
        case 'whatsapp':
            shareUrl = 'https://wa.me/?text=' + encoded;
            break;

        case 'telegram':
            shareUrl = 'https://t.me/share/url?url=' + encUrl + '&text=' + encText;
            break;

        case 'email':
            shareUrl = 'mailto:?subject=' + encTitle + '&body=' + encoded;
            break;

        case 'facebook':
            shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encUrl + '&quote=' + encText;
            break;

        case 'twitter':
            shareUrl = 'https://twitter.com/intent/tweet?text=' + encText + '&url=' + encUrl;
            break;

        case 'linkedin':
            shareUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' + encUrl + '&summary=' + encText;
            break;

        default:
            return;
    }

    window.open(shareUrl, '_blank', 'noopener,noreferrer,width=640,height=500');
});

// ── Copy link ─────────────────────────────────────────────────────────────────
$(document).on('click', '#shareCopyBtn', function () {
    var url = $('#shareCopyUrl').val();
    var $btn = $(this);

    var done = function () {
        $btn.html('<i class="ri-check-line me-1"></i>Copied!');
        setTimeout(function () {
            $btn.html('<i class="ri-clipboard-line me-1"></i>Copy');
        }, 2200);
    };

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(done).catch(function () {
            fallbackCopy(url, done);
        });
    } else {
        fallbackCopy(url, done);
    }
});

function fallbackCopy(text, callback) {
    var el = document.getElementById('shareCopyUrl');
    el.removeAttribute('readonly');
    el.select();
    el.setSelectionRange(0, 99999);
    try { document.execCommand('copy'); } catch (e) {}
    el.setAttribute('readonly', '');
    if (typeof callback === 'function') callback();
}
