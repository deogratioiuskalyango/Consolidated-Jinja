@php
    $unreadCount = auth()->user()->shareholder?->notifications()->where('is_read', false)->count() ?? 0;
@endphp

{{-- Overlay backdrop — tap outside closes sidebar on mobile --}}
<div class="sh-sidebar-overlay" id="sh-sidebar-overlay"></div>

<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">

                <li class="{{ @$navDashboardActiveClass }}">
                    <a href="{{ route('shareholder.dashboard') }}">
                        <i class="ri-dashboard-line"></i>
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                </li>

                <li class="{{ @$navResolutionsActiveClass }}">
                    <a href="{{ route('shareholder.resolutions.index') }}">
                        <i class="ri-survey-line"></i>
                        <span>{{ __('Resolutions & Voting') }}</span>
                    </a>
                </li>

                <li class="{{ @$navFinancialApprovalsActiveClass }}">
                    <a href="{{ route('shareholder.financial-approvals.index') }}">
                        <i class="ri-checkbox-circle-line"></i>
                        <span>{{ __('Financial Approvals') }}</span>
                    </a>
                </li>

                <li class="{{ @$navDocumentsActiveClass }}">
                    <a href="{{ route('shareholder.documents.index') }}">
                        <i class="ri-folder-lock-line"></i>
                        <span>{{ __('Company Documents') }}</span>
                    </a>
                </li>

                <li class="{{ @$navDividendsActiveClass }}">
                    <a href="{{ route('shareholder.dividends.index') }}">
                        <i class="ri-coins-line"></i>
                        <span>{{ __('Dividends') }}</span>
                    </a>
                </li>

                <li class="{{ @$navMeetingsActiveClass }}">
                    <a href="{{ route('shareholder.meetings.index') }}">
                        <i class="ri-calendar-event-line"></i>
                        <span>{{ __('Meetings') }}</span>
                    </a>
                </li>

                <li class="{{ @$navShareTransfersActiveClass }}">
                    <a href="{{ route('shareholder.share-transfers.index') }}">
                        <i class="ri-exchange-funds-line"></i>
                        <span>{{ __('Share Transfers') }}</span>
                    </a>
                </li>

                <li class="{{ @$navGovernanceRightsActiveClass }}">
                    <a href="{{ route('shareholder.governance-rights') }}">
                        <i class="ri-shield-check-line"></i>
                        <span>{{ __('My Rights') }}</span>
                    </a>
                </li>

                <li class="{{ @$navNotificationsActiveClass }}">
                    <a href="{{ route('shareholder.notifications.index') }}">
                        <i class="ri-notification-2-line"></i>
                        <span>{{ __('Notifications') }}</span>
                        @if($unreadCount > 0)
                            <span class="badge bg-danger rounded-pill ms-auto">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                        @endif
                    </a>
                </li>

                <li class="{{ @$navProfileMMShowClass }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="ri-user-settings-line"></i>
                        <span>{{ __('Account') }}</span>
                    </a>
                    <ul class="sub-menu {{ @$navProfileMMShowClass }}" aria-expanded="false">
                        <li class="{{ @$navProfileActiveClass }}">
                            <a href="{{ route('shareholder.profile') }}" class="{{ @$navProfileActiveClass }}">{{ __('My Profile') }}</a>
                        </li>
                        <li class="{{ @$navChangePasswordActiveClass }}">
                            <a href="{{ route('shareholder.change-password') }}" class="{{ @$navChangePasswordActiveClass }}">{{ __('Change Password') }}</a>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</div>
