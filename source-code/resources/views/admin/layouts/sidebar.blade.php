<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">
                <li>
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="ri-dashboard-line"></i>
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                </li>
                @if (isAddonInstalled('PROTYSAAS') > 3)
                    <li>
                        <a href="{{ route('admin.packages.index') }}">
                            <i class="ri-price-tag-3-line"></i>
                            <span>{{ __('Packages') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.subscriptions.orders') }}">
                            <i class="ri-shopping-bag-3-line"></i>
                            <span>{{ __('All Orders') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.packages.owner') }}">
                            <i class="ri-file-user-line"></i>
                            <span>{{ __('Owner Packages') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.message.index') }}">
                            <i class="ri-chat-1-line"></i>
                            <span>{{ __('Message') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i class="ri-file-shield-2-line"></i>
                            <span>{{ __('Manage Policy') }}</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a
                                    href="{{ route('admin.setting.terms-conditions') }}">{{ __('Terms & Conditions') }}</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.setting.privacy-policy') }}">{{ __('Privacy Policy') }}</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.setting.cookie-policy') }}">{{ __('Cookie Policy') }}</a>
                            </li>
                        </ul>
                    </li>
                @endif
                <li>
                    <a href="{{ route('admin.owner.index') }}">
                        <i class="ri-user-star-line"></i>
                        <span>{{ __('Owner') }}</span>
                    </a>
                </li>
                @php $pendingDeletions = \App\Models\AccountDeletionRequest::pending()->count(); @endphp
                <li>
                    <a href="{{ route('admin.deletion-requests.index') }}">
                        <i class="ri-user-unfollow-line"></i>
                        <span>{{ __('Deletion Requests') }}</span>
                        @if($pendingDeletions > 0)
                            <span class="badge bg-danger rounded-pill ms-auto">
                                {{ $pendingDeletions > 99 ? '99+' : $pendingDeletions }}
                            </span>
                        @endif
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.contracts.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.contracts.index') }}" class="{{ request()->routeIs('admin.contracts.*') ? 'active' : '' }}">
                        <i class="ri-draft-line"></i>
                        <span>{{ __('Contracts') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.shareholders.index') }}">
                        <i class="ri-stock-line"></i>
                        <span>{{ __('Shareholders') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.accountants.index') }}">
                        <i class="ri-calculator-line"></i>
                        <span>{{ __('Accountants') }}</span>
                    </a>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow {{ @$navGovernanceMMShowClass }}">
                        <i class="ri-scales-3-line"></i>
                        <span>{{ __('Governance') }}</span>
                    </a>
                    <ul class="sub-menu {{ @$navGovernanceMMShowClass }}" aria-expanded="false">
                        <li class="{{ @$subNavResolutionsActiveClass }}">
                            <a href="{{ route('admin.governance.resolutions.index') }}" class="{{ @$subNavResolutionsActiveClass }}">{{ __('Resolutions') }}</a>
                        </li>
                        <li class="{{ @$subNavApprovalsActiveClass }}">
                            <a href="{{ route('admin.governance.approvals.index') }}" class="{{ @$subNavApprovalsActiveClass }}">{{ __('Financial Approvals') }}</a>
                        </li>
                        <li class="{{ @$subNavDocumentsActiveClass }}">
                            <a href="{{ route('admin.governance.documents.index') }}" class="{{ @$subNavDocumentsActiveClass }}">{{ __('Documents') }}</a>
                        </li>
                        <li class="{{ @$subNavDividendsActiveClass }}">
                            <a href="{{ route('admin.governance.dividends.index') }}" class="{{ @$subNavDividendsActiveClass }}">{{ __('Dividends') }}</a>
                        </li>
                        <li class="{{ @$subNavMeetingsActiveClass }}">
                            <a href="{{ route('admin.governance.meetings.index') }}" class="{{ @$subNavMeetingsActiveClass }}">{{ __('Meetings') }}</a>
                        </li>
                        <li class="{{ @$subNavAuditLogsActiveClass }}">
                            <a href="{{ route('admin.governance.audit-logs') }}" class="{{ @$subNavAuditLogsActiveClass }}">{{ __('Audit Logs') }}</a>
                        </li>
                        <li class="{{ @$subNavShareClassesActiveClass }}">
                            <a href="{{ route('admin.share-classes.index') }}" class="{{ @$subNavShareClassesActiveClass }}">{{ __('Share Classes') }}</a>
                        </li>
                        <li class="{{ @$subNavGovernanceRulesActiveClass }}">
                            <a href="{{ route('admin.governance-rules.index') }}" class="{{ @$subNavGovernanceRulesActiveClass }}">{{ __('Governance Rules') }}</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('admin.setting.general-setting') }}">
                        <i class="ri-settings-3-line"></i>
                        <span>{{ __('Settings') }}</span>
                    </a>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="ri-user-line"></i>
                        <span>{{ __('Profile') }}</span>
                    </a>
                    <ul class="sub-menu {{ @$navProfileMMShowClass }}" aria-expanded="false">
                        <li class="{{ @$subNavProfileMMActiveClass }}"><a class="{{ @$subNavProfileActiveClass }}"
                                href="{{ route('profile') }}">{{ __('My Profile') }}</a></li>
                        <li><a href="{{ route('change-password') }}">{{ __('Change Password') }}</a></li>
                    </ul>
                </li>
                <li class="{{ request()->routeIs('admin.addons.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.addons.index') }}"
                        class="{{ request()->routeIs('admin.addons.*') ? 'active' : '' }}">
                        <i class="ri-puzzle-line"></i>
                        <span>{{ __('Addons') }}</span>
                    </a>
                </li>
                <li class="font-semi-bold mt-20 text-center text-info">
                    <a href="">
                        <span>
                            {{ __('Current Version') }} :
                        </span>
                        {{ getOption('current_version', 'v1.0') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
