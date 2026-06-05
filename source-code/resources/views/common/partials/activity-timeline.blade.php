@if(!empty($auditLogs) && $auditLogs->count())
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0"><i class="ri-history-line me-2 text-info"></i>Recent Activity</h6>
    </div>
    <div class="card-body py-2">
        <div class="timeline-list" style="max-height:320px;overflow-y:auto;">
            @php
            $actionColors = [
                'payment_recorded'    => 'success',
                'payment_reversed'    => 'danger',
                'expense_created'     => 'warning',
                'expense_approved'    => 'info',
                'expense_rejected'    => 'danger',
                'report_generated'    => 'primary',
                'report_viewed'       => 'secondary',
                'reconciliation'      => 'info',
                'suspend_accountant'  => 'warning',
            ];
            $actionIcons = [
                'payment_recorded'   => 'ri-money-dollar-circle-line',
                'payment_reversed'   => 'ri-arrow-go-back-line',
                'expense_created'    => 'ri-receipt-line',
                'expense_approved'   => 'ri-checkbox-circle-line',
                'report_generated'   => 'ri-file-chart-line',
                'reconciliation'     => 'ri-scales-3-line',
            ];
            @endphp
            @foreach($auditLogs as $log)
            @php
                $color = $actionColors[$log->action] ?? 'secondary';
                $icon  = $actionIcons[$log->action] ?? 'ri-time-line';
                $label = ucwords(str_replace('_', ' ', $log->action));
            @endphp
            <div class="d-flex gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div class="flex-shrink-0">
                    <span class="badge rounded-circle bg-{{ $color }} bg-opacity-15 text-{{ $color }} p-2" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;">
                        <i class="{{ $icon }}" style="font-size:14px;"></i>
                    </span>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="d-flex justify-content-between align-items-start">
                        <span class="fw-semibold small text-truncate">{{ $label }}</span>
                        <span class="text-muted" style="font-size:11px;white-space:nowrap;margin-left:8px;">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-muted mb-0" style="font-size:12px;">{{ Str::limit($log->note ?? '', 60) }}</p>
                    @if($log->actor_name)
                    <span class="badge bg-light text-dark" style="font-size:10px;">{{ $log->actor_name }}</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-2">
            <a href="{{ route('accountant.audit-logs') }}" class="small text-primary">View Full Audit Trail &rarr;</a>
        </div>
    </div>
</div>
@endif
