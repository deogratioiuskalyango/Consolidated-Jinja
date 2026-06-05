@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">

                {{-- Page Header --}}
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between border-bottom mb-20">
                            <div class="page-title-left">
                                <h3 class="mb-sm-0">{{ $pageTitle }}</h3>
                            </div>
                            <div class="page-title-right">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                    <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ============================= GOVERNANCE RULES ============================= --}}
                <div class="card mb-4">
                    <div class="card-header bg-light d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-semibold"><i class="ri-git-branch-line me-2"></i>{{ __('Governance Rules') }}</h5>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addRuleModal">
                            <i class="ri-add-line me-1"></i>{{ __('Add Rule') }}
                        </button>
                    </div>
                    <div class="card-body p-0">
                        @if($rules->isEmpty())
                            <div class="text-center text-muted py-5">
                                <i class="ri-git-branch-line fs-2 d-block mb-2"></i>
                                {{ __('No governance rules configured yet.') }}
                            </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">{{ __('Rule Key') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th class="text-center">{{ __('Status') }}</th>
                                        <th class="text-center">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rules as $rule)
                                    @php
                                        $typeBadge = [
                                            'voting'   => 'bg-primary',
                                            'approval' => 'bg-warning text-dark',
                                            'transfer' => 'bg-info',
                                            'dividend' => 'bg-success',
                                            'general'  => 'bg-secondary',
                                        ][$rule->rule_type] ?? 'bg-secondary';
                                    @endphp
                                    <tr>
                                        <td class="ps-3"><code class="text-primary">{{ $rule->rule_key }}</code></td>
                                        <td class="fw-medium">{{ $rule->rule_name }}</td>
                                        <td><span class="badge {{ $typeBadge }}">{{ ucfirst($rule->rule_type) }}</span></td>
                                        <td class="text-muted small">{{ Str::limit($rule->description, 80) ?? '—' }}</td>
                                        <td class="text-center">
                                            @if($rule->is_active)
                                                <span class="badge bg-success">{{ __('Active') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('admin.governance-rules.rule.toggle', $rule) }}" method="POST" class="ajax d-inline" data-handler="getShowMessage">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $rule->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                    title="{{ $rule->is_active ? __('Deactivate') : __('Activate') }}">
                                                    <i class="ri-{{ $rule->is_active ? 'pause' : 'play' }}-circle-line"></i>
                                                    {{ $rule->is_active ? __('Deactivate') : __('Activate') }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- ============================= APPROVAL THRESHOLDS ============================= --}}
                <div class="card">
                    <div class="card-header bg-light d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-semibold"><i class="ri-bar-chart-grouped-line me-2"></i>{{ __('Approval Thresholds') }}</h5>
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addThresholdModal">
                            <i class="ri-add-line me-1"></i>{{ __('Add Threshold') }}
                        </button>
                    </div>
                    <div class="card-body p-0">
                        @if($thresholds->isEmpty())
                            <div class="text-center text-muted py-5">
                                <i class="ri-bar-chart-grouped-line fs-2 d-block mb-2"></i>
                                {{ __('No approval thresholds configured yet.') }}
                            </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">{{ __('Name') }}</th>
                                        <th>{{ __('Amount Range') }}</th>
                                        <th>{{ __('Required Classes') }}</th>
                                        <th class="text-center">{{ __('Min Approvers') }}</th>
                                        <th class="text-center">{{ __('Requires Quorum') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th class="text-center">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($thresholds as $threshold)
                                    @php
                                        $classBadgeMap = ['A'=>'bg-purple','B'=>'bg-primary','C'=>'bg-success','D'=>'bg-secondary','E'=>'bg-warning text-dark','F'=>'bg-danger'];
                                        $classes = is_array($threshold->required_approver_classes)
                                            ? $threshold->required_approver_classes
                                            : (json_decode($threshold->required_approver_classes, true) ?? []);
                                    @endphp
                                    <tr>
                                        <td class="ps-3 fw-medium">{{ $threshold->name }}</td>
                                        <td>
                                            <span class="text-success fw-medium">{{ number_format($threshold->min_amount, 2) }}</span>
                                            @if($threshold->max_amount)
                                                <span class="text-muted"> — </span>
                                                <span class="text-danger fw-medium">{{ number_format($threshold->max_amount, 2) }}</span>
                                            @else
                                                <span class="text-muted"> — {{ __('Unlimited') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($classes as $cls)
                                                    <span class="badge {{ $classBadgeMap[$cls] ?? 'bg-secondary' }}">{{ $cls }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info fs-6">{{ $threshold->min_approver_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($threshold->requires_quorum)
                                                <span class="badge bg-warning text-dark">{{ __('Yes') }} ({{ $threshold->quorum_percentage }}%)</span>
                                            @else
                                                <span class="badge bg-light text-muted border">{{ __('No') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-muted small">{{ Str::limit($threshold->description, 60) ?? '—' }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('admin.governance-rules.threshold.destroy', $threshold) }}"
                                                  method="POST" class="ajax d-inline" data-handler="getShowMessage"
                                                  onsubmit="return confirm('{{ __('Delete this threshold?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ============================= ADD RULE MODAL ============================= --}}
<div class="modal fade" id="addRuleModal" tabindex="-1" aria-labelledby="addRuleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRuleModalLabel"><i class="ri-git-branch-line me-2"></i>{{ __('Add / Update Governance Rule') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <form action="{{ route('admin.governance-rules.rule.store') }}" method="POST" class="ajax" data-handler="getShowMessage">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">{{ __('Rule Key') }} <span class="text-danger">*</span></label>
                            <input type="text" name="rule_key" class="form-control" required
                                placeholder="{{ __('e.g. vote_majority_required') }}"
                                pattern="[a-zA-Z0-9_]+" title="{{ __('Alphanumeric and underscores only') }}">
                            <div class="form-text">{{ __('Unique identifier. Alphanumeric + underscores.') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">{{ __('Rule Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="rule_name" class="form-control" required placeholder="{{ __('Human-readable name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">{{ __('Rule Type') }} <span class="text-danger">*</span></label>
                            <select name="rule_type" class="form-select" required>
                                <option value="">{{ __('Select type') }}</option>
                                <option value="voting">{{ __('Voting') }}</option>
                                <option value="approval">{{ __('Approval') }}</option>
                                <option value="transfer">{{ __('Transfer') }}</option>
                                <option value="dividend">{{ __('Dividend') }}</option>
                                <option value="general">{{ __('General') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">{{ __('Active') }}</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="ruleIsActive" value="1" checked>
                                <label class="form-check-label" for="ruleIsActive">{{ __('Enable this rule') }}</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">{{ __('Description') }}</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="{{ __('Describe what this rule does...') }}"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">{{ __('Config (JSON)') }}</label>
                            <textarea name="config" class="form-control font-monospace" rows="4"
                                placeholder='{{ __('e.g. {"threshold": 51, "require_board": true}') }}'></textarea>
                            <div class="form-text">{{ __('Optional JSON configuration object for this rule.') }}</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary"><i class="ri-save-line me-1"></i>{{ __('Save Rule') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================= ADD THRESHOLD MODAL ============================= --}}
<div class="modal fade" id="addThresholdModal" tabindex="-1" aria-labelledby="addThresholdModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addThresholdModalLabel"><i class="ri-bar-chart-grouped-line me-2"></i>{{ __('Add / Update Approval Threshold') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <form action="{{ route('admin.governance-rules.threshold.store') }}" method="POST" class="ajax" data-handler="getShowMessage">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium">{{ __('Threshold Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="{{ __('e.g. Large Capital Expenditure') }}">
                            <div class="form-text">{{ __('Used as the unique identifier — existing threshold with same name will be updated.') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">{{ __('Minimum Amount') }} <span class="text-danger">*</span></label>
                            <input type="number" name="min_amount" class="form-control" required min="0" step="0.01" placeholder="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">{{ __('Maximum Amount') }}</label>
                            <input type="number" name="max_amount" class="form-control" min="0" step="0.01" placeholder="{{ __('Leave blank for unlimited') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">{{ __('Required Approver Classes') }} <span class="text-danger">*</span></label>
                            <select name="required_approver_classes[]" class="form-select" multiple required size="6">
                                @foreach(['A','B','C','D','E','F'] as $cls)
                                    <option value="{{ $cls }}">{{ __('Class') }} {{ $cls }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">{{ __('Hold Ctrl / Cmd to select multiple classes.') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">{{ __('Min Approver Count') }} <span class="text-danger">*</span></label>
                            <input type="number" name="min_approver_count" class="form-control" required min="1" value="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">{{ __('Required Ownership % (optional)') }}</label>
                            <input type="number" name="required_ownership_percentage" class="form-control" min="0" max="100" step="0.01" placeholder="{{ __('No minimum') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">{{ __('Requires Quorum') }}</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="requires_quorum" id="requiresQuorum" value="1"
                                    onchange="document.getElementById('quorumPctGroup').style.display = this.checked ? 'block' : 'none'">
                                <label class="form-check-label" for="requiresQuorum">{{ __('Yes, quorum required') }}</label>
                            </div>
                        </div>
                        <div class="col-md-6" id="quorumPctGroup" style="display:none">
                            <label class="form-label fw-medium">{{ __('Quorum Percentage (%)') }}</label>
                            <input type="number" name="quorum_percentage" class="form-control" min="0" max="100" step="0.01" placeholder="51">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">{{ __('Description') }}</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="{{ __('Optional notes about this threshold...') }}"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success"><i class="ri-save-line me-1"></i>{{ __('Save Threshold') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .bg-purple { background-color: #6f42c1 !important; color: #fff !important; }
    .font-monospace { font-family: 'Courier New', monospace; font-size: 0.85rem; }
    select[multiple] option { padding: 4px 8px; }
    select[multiple] option:checked { background: #0d6efd; color: #fff; }
</style>
@endpush

@push('script')
<script>
    // Re-show quorum group if modal is dismissed and re-opened
    document.getElementById('addThresholdModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('requiresQuorum').checked = false;
        document.getElementById('quorumPctGroup').style.display = 'none';
    });
</script>
@endpush
