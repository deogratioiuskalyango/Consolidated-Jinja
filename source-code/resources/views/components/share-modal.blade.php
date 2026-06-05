{{-- ================================================================
     Reusable Share Modal
     Usage (JS):  openShareModal(title, text, url)
     ================================================================ --}}
<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content">

            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title d-flex align-items-center gap-2" id="shareModalLabel">
                    <i class="ri-share-forward-2-line text-primary"></i>
                    <span id="shareModalHeading">{{ __('Share') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>

            <div class="modal-body pt-2">

                {{-- Preview --}}
                <div class="rounded p-3 mb-4 small text-muted border"
                     id="sharePreviewText"
                     style="background:#f8fafc; max-height:72px; overflow:hidden; line-clamp:3; -webkit-line-clamp:3;"></div>

                {{-- Platform grid --}}
                <div class="row g-2 text-center">

                    {{-- WhatsApp --}}
                    <div class="col-4">
                        <button type="button"
                                class="btn btn-light w-100 share-platform-btn d-flex flex-column align-items-center py-3 gap-1 rounded"
                                data-platform="whatsapp">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 48 48">
                                <radialGradient id="wa-a" cx="11.787" cy="36.213" r="38.71" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" stop-color="#57d163"/>
                                    <stop offset=".48" stop-color="#23b33a"/>
                                    <stop offset=".99" stop-color="#1f961f"/>
                                </radialGradient>
                                <path fill="url(#wa-a)" d="M24 4C13 4 4 13 4 24c0 3.7 1 7.2 2.9 10.2L4 44l10.1-2.9C17.1 42.9 20.5 44 24 44c11 0 20-9 20-20S35 4 24 4z"/>
                                <path fill="#fff" d="M33.5 28.8c-.4-.2-2.5-1.2-2.9-1.4-.4-.2-.7-.2-1 .2-.3.4-1.1 1.4-1.4 1.7-.3.3-.5.4-1 .1s-1.9-.7-3.6-2.2c-1.3-1.2-2.2-2.6-2.5-3-.3-.4 0-.7.2-.9.2-.2.4-.5.7-.8.2-.3.3-.5.5-.8.2-.3.1-.6 0-.8-.1-.2-1-2.5-1.4-3.4-.4-.9-.8-.8-1-.8h-.9c-.3 0-.8.1-1.2.6-.4.5-1.6 1.5-1.6 3.7s1.6 4.3 1.9 4.6c.2.3 3.2 4.9 7.8 6.9 1.1.5 1.9.7 2.6.9 1.1.3 2.1.3 2.9.2.9-.1 2.7-1.1 3.1-2.2.4-1.1.4-2 .3-2.2-.1-.2-.5-.4-.9-.6z"/>
                            </svg>
                            <span class="small fw-medium">WhatsApp</span>
                        </button>
                    </div>

                    {{-- Telegram --}}
                    <div class="col-4">
                        <button type="button"
                                class="btn btn-light w-100 share-platform-btn d-flex flex-column align-items-center py-3 gap-1 rounded"
                                data-platform="telegram">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 48 48">
                                <circle cx="24" cy="24" r="20" fill="#29b6f6"/>
                                <path fill="#fff" d="M33.95 15l-3.746 19.126s-.161.554-.666.578-.959-.294-.959-.294l-6.129-5.097-2.919 1.538L18.918 24l11.352-5.708s.494-.34.494-.7c0-.245-.284-.378-.284-.378L14.05 22.583l-2.96-.826s-.521-.285-.521-.709c0-.426.578-.683.578-.683l21.1-8.29S33.95 11.667 33.95 15z"/>
                            </svg>
                            <span class="small fw-medium">Telegram</span>
                        </button>
                    </div>

                    {{-- Email --}}
                    <div class="col-4">
                        <button type="button"
                                class="btn btn-light w-100 share-platform-btn d-flex flex-column align-items-center py-3 gap-1 rounded"
                                data-platform="email">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 48 48">
                                <rect width="40" height="30" x="4" y="9" rx="3" fill="#E53935"/>
                                <path fill="#fff" d="M4 12l20 13L44 12v3L24 28 4 15z"/>
                            </svg>
                            <span class="small fw-medium">{{ __('Email') }}</span>
                        </button>
                    </div>

                    {{-- Facebook --}}
                    <div class="col-4">
                        <button type="button"
                                class="btn btn-light w-100 share-platform-btn d-flex flex-column align-items-center py-3 gap-1 rounded"
                                data-platform="facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 48 48">
                                <circle cx="24" cy="24" r="20" fill="#1877F2"/>
                                <path fill="#fff" d="M26.145 38V25.638h4.125l.618-4.79h-4.743V17.88c0-1.387.385-2.332 2.374-2.332h2.536V11.22a33.9 33.9 0 00-3.697-.189c-3.659 0-6.163 2.233-6.163 6.334v3.483H17v4.79h4.195V38h4.95z"/>
                            </svg>
                            <span class="small fw-medium">Facebook</span>
                        </button>
                    </div>

                    {{-- X / Twitter --}}
                    <div class="col-4">
                        <button type="button"
                                class="btn btn-light w-100 share-platform-btn d-flex flex-column align-items-center py-3 gap-1 rounded"
                                data-platform="twitter">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 48 48">
                                <circle cx="24" cy="24" r="20" fill="#000"/>
                                <path fill="#fff" d="M26.34 21.94L34.14 13h-1.85l-6.78 7.88L20.07 13H13l8.17 11.9L13 35h1.85l7.14-8.3 5.71 8.3H35l-8.66-13.06zM22.86 25.68l-.83-1.18-6.6-9.44h2.84l5.3 7.58.83 1.18 6.92 9.9h-2.84l-5.62-8.04z"/>
                            </svg>
                            <span class="small fw-medium">X / Twitter</span>
                        </button>
                    </div>

                    {{-- LinkedIn --}}
                    <div class="col-4">
                        <button type="button"
                                class="btn btn-light w-100 share-platform-btn d-flex flex-column align-items-center py-3 gap-1 rounded"
                                data-platform="linkedin">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 48 48">
                                <rect width="40" height="40" x="4" y="4" rx="4" fill="#0A66C2"/>
                                <path fill="#fff" d="M14.55 19.5h4.5V34h-4.5zm2.25-7a2.6 2.6 0 110 5.2 2.6 2.6 0 010-5.2zM22 19.5h4.32v2h.06c.6-1.14 2.07-2.34 4.26-2.34 4.56 0 5.4 3 5.4 6.9V34h-4.5v-7.1c0-1.69-.03-3.87-2.36-3.87-2.36 0-2.72 1.85-2.72 3.75V34H22z"/>
                            </svg>
                            <span class="small fw-medium">LinkedIn</span>
                        </button>
                    </div>

                </div>

                {{-- Copy link row --}}
                <div class="mt-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="ri-links-line text-muted"></i>
                        </span>
                        <input type="text"
                               class="form-control bg-light border-start-0 border-end-0 font-monospace"
                               id="shareCopyUrl"
                               readonly>
                        <button class="btn btn-outline-secondary" type="button" id="shareCopyBtn">
                            <i class="ri-clipboard-line me-1"></i>{{ __('Copy') }}
                        </button>
                    </div>
                </div>

            </div>{{-- /modal-body --}}
        </div>
    </div>
</div>
