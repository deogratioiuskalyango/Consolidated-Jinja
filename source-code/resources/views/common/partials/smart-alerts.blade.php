@if(!empty($alerts))
<div class="row mb-3" id="smart-alerts-panel">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-2">
                <h6 class="mb-0"><i class="ri-notification-3-line me-2 text-warning"></i>Smart Alerts</h6>
                <button type="button" class="btn-close btn-sm" onclick="document.getElementById('smart-alerts-panel').style.display='none'"></button>
            </div>
            <div class="card-body py-2">
                @foreach($alerts as $alert)
                <div class="d-flex align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="flex-shrink-0 me-3">
                        <span class="badge rounded-pill bg-{{ $alert['type'] }} p-2">
                            <i class="{{ $alert['icon'] ?? 'ri-alert-line' }} fs-6"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <p class="mb-0 small">{{ $alert['message'] }}</p>
                    </div>
                    @if(!empty($alert['action']))
                    <a href="{{ $alert['action'] }}" class="btn btn-sm btn-outline-{{ $alert['type'] }} ms-2 flex-shrink-0">View</a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
