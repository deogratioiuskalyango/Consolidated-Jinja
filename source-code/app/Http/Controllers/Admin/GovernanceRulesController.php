<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GovernanceRule;
use App\Models\ApprovalThreshold;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class GovernanceRulesController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        $ownerId = auth()->id();
        $data['pageTitle']  = __('Governance Rules');
        $data['rules']      = GovernanceRule::where('owner_user_id', $ownerId)->orderBy('rule_type')->get();
        $data['thresholds'] = ApprovalThreshold::where('owner_user_id', $ownerId)->orderBy('min_amount')->get();
        return view('admin.governance-rules.index', $data);
    }

    public function storeRule(Request $request)
    {
        $request->validate([
            'rule_key'    => 'required|max:100',
            'rule_name'   => 'required|max:191',
            'rule_type'   => 'required|in:voting,approval,transfer,dividend,general',
            'description' => 'nullable',
            'config'      => 'nullable|json',
        ]);

        GovernanceRule::updateOrCreate(
            ['owner_user_id' => auth()->id(), 'rule_key' => $request->rule_key],
            [
                'rule_name'   => $request->rule_name,
                'rule_type'   => $request->rule_type,
                'description' => $request->description,
                'config'      => $request->config ? json_decode($request->config, true) : null,
                'is_active'   => $request->boolean('is_active', true),
            ]
        );

        return $this->success([], __('Rule saved.'));
    }

    public function toggleRule(GovernanceRule $rule)
    {
        abort_if($rule->owner_user_id !== auth()->id(), 403);
        $rule->update(['is_active' => !$rule->is_active]);
        return $this->success([], $rule->is_active ? __('Rule activated.') : __('Rule deactivated.'));
    }

    public function storeThreshold(Request $request)
    {
        $request->validate([
            'name'                          => 'required|max:191',
            'min_amount'                    => 'required|numeric|min:0',
            'max_amount'                    => 'nullable|numeric|min:0',
            'required_approver_classes'     => 'required|array|min:1',
            'required_approver_classes.*'   => 'in:A,B,C,D,E,F',
            'min_approver_count'            => 'required|integer|min:1',
            'required_ownership_percentage' => 'nullable|numeric|min:0|max:100',
            'requires_quorum'               => 'boolean',
            'quorum_percentage'             => 'nullable|numeric|min:0|max:100',
            'description'                   => 'nullable',
        ]);

        $threshold = ApprovalThreshold::updateOrCreate(
            ['owner_user_id' => auth()->id(), 'name' => $request->name],
            $request->except('_token') + ['owner_user_id' => auth()->id(), 'is_active' => true]
        );

        return $this->success([], __('Threshold saved.'));
    }

    public function destroyThreshold(ApprovalThreshold $threshold)
    {
        abort_if($threshold->owner_user_id !== auth()->id(), 403);
        $threshold->delete();
        return $this->success([], __('Threshold deleted.'));
    }
}
