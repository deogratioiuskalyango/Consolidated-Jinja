<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">

                <li>
                    <a href="{{ route('owner.dashboard') }}">
                        <i class="ri-dashboard-line"></i>
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                </li>
                @can('Manage Property')
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i class="ri-building-line"></i>
                            <span>{{ __('Properties') }}</span>
                        </a>
                        <ul class="sub-menu {{ @$navPropertyMMShowClass }}" aria-expanded="false">
                            <li class="{{ @$subNavAllPropertyMMActiveClass }}">
                                <a href="{{ route('owner.property.allProperty') }}"
                                    class="{{ @$subNavAllPropertyActiveClass }}">{{ __('All Property') }}</a>
                            </li>
                            <li class="{{ @$subNavAllUnitMMActiveClass }}">
                                <a href="{{ route('owner.property.allUnit') }}"
                                    class="{{ @$subNavAllUnitActiveClass }}">{{ __('All Unit') }}</a>
                            </li>
                            <li class="{{ @$subNavOwnPropertyActiveClass }}">
                                <a href="{{ route('owner.property.ownProperty') }}"
                                    class="{{ @$subNavOwnPropertyActiveClass }}">{{ __('Own Property') }}</a>
                            </li>
                            <li class="{{ @$subNavLeasePropertyActiveClass }}">
                                <a href="{{ route('owner.property.leaseProperty') }}"
                                    class="{{ @$subNavLeasePropertyActiveClass }}">{{ __('Lease Property') }}</a>
                            </li>
                        </ul>
                    </li>
                @endcan
                @can('Manage Tenant')
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i class="ri-user-3-line"></i>
                            <span>{{ __('Tenants') }}</span>
                        </a>
                        <ul class="sub-menu {{ @$navTenantMMShowClass }}" aria-expanded="false">
                            <li class="{{ @$subNavAllTenantMMActiveClass }}">
                                <a href="{{ route('owner.tenant.index', ['type' => 'all']) }}"
                                    class="{{ @$subNavAllTenantActiveClass }}">{{ __('All Tenants') }}</a>
                            </li>
                            <li class="{{ @$subNavTenantHistoryMMActiveClass }}">
                                <a href="{{ route('owner.tenant.index', ['type' => 'history']) }}"
                                    class="{{ @$subNavTenantHistoryActiveClass }}">{{ __('Tenant History') }}</a>
                            </li>
                        </ul>
                    </li>
                @endcan
                @can('Manage Billing')
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i class="ri-wallet-line"></i>
                            <span>{{ __('Billing Center') }}</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li class="">
                                <a href="{{ route('owner.invoice.index') }}" class="">{{ __('All Invoices') }}</a>
                            </li>
                            <li class="">
                                <a href="{{ route('owner.invoice.recurring-setting.index') }}"
                                    class="">{{ __('Recurring Setting') }}</a>
                            </li>
                        </ul>
                    </li>
                @endcan
                @can('Manage Expenses')
                    <li>
                        <a href="{{ route('owner.expense.index') }}">
                            <i class="ri-receipt-line"></i>
                            <span>{{ __('Expenses') }}</span>
                        </a>
                    </li>
                @endcan
                <li>
                    <a href="{{ route('owner.chats.index') }}">
                        <i class="ri-chat-3-line"></i>
                        <span>{{ __('Chats') }}</span>
                    </a>
                </li>
                @can('Manage Documents')
                    <li>
                        <a href="{{ route('owner.documents.index') }}">
                            <i class="ri-file-text-line"></i>
                            <span>{{ __('Documents') }}</span>
                        </a>
                    </li>
                @endcan
                @can('Manage Information')
                    <li>
                        <a href="{{ route('owner.information.index') }}">
                            <i class="ri-information-line"></i>
                            <span>{{ __('Information') }}</span>
                        </a>
                    </li>
                @endcan
                @if (isAddonInstalled('PROTYLISTING') > 0)
                    @if (getOption('LISTING_STATUS', 0) == ACTIVE)
                        @can('Manage Listing')
                            <li>
                                <a href="javascript: void(0);" class="has-arrow">
                                    <i class="ri-price-tag-3-line"></i>
                                    <span>{{ __('My Listing') }}</span>
                                </a>
                                <ul class="sub-menu" aria-expanded="false">
                                    <li>
                                        <a href="{{ route('owner.listing.create') }}">{{ __('Upload List') }}</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('owner.listing.index') }}">{{ __('All List') }}</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('owner.listing.contact') }}">{{ __('Contact List') }}</a>
                                    </li>
                                </ul>
                            </li>
                        @endcan
                    @endif
                @endif
                @can('Manage Maintains')
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i class="ri-tools-line"></i>
                            <span>{{ __('Maintenance') }}</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('owner.maintainer.index') }}">{{ __('Maintenance Persons') }}</a></li>
                            <li><a
                                    href="{{ route('owner.maintenance-request.index') }}">{{ __('Maintenance Request') }}</a>
                            </li>
                        </ul>
                    </li>
                @endcan
                @if (isAddonInstalled('PROTYSAAS') < 1 || ownerCurrentPackage(getOwnerUserId())?->ticket_support == ACTIVE)
                    @can('Manage Ticket')
                        <li>
                            <a href="{{ route('owner.ticket.index') }}">
                                <i class="ri-customer-service-2-line"></i>
                                <span>{{ __('Tickets') }}</span>
                            </a>
                        </li>
                    @endcan
                @endif
                @if (isAddonInstalled('PROTYSAAS') < 1 || ownerCurrentPackage(getOwnerUserId())?->notice_support == ACTIVE)
                    @can('Manage Noticeboard')
                        <li>
                            <a href="{{ route('owner.noticeboard.index') }}">
                                <i class="ri-megaphone-line"></i>
                                <span>{{ __('Notice Board') }}</span>
                            </a>
                        </li>
                    @endcan
                @endif
                @can('Manage Report')
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i class="ri-bar-chart-2-line"></i>
                            <span>{{ __('Report') }}</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="{{ route('owner.reports.earning') }}">{{ __('Earning') }}</a>
                            </li>
                            <li>
                                <a
                                    href="{{ route('owner.reports.loss-profit.by.month') }}">{{ __('Loss / Profit By Month') }}</a>
                            </li>
                            <li>
                                <a href="{{ route('owner.reports.expenses') }}">{{ __('Expenses') }}</a>
                            </li>
                            {{-- <li>
                                <a href="{{ route('owner.reports.lease') }}">{{ __('Lease') }}</a>
                            </li> --}}
                            <li>
                                <a href="{{ route('owner.reports.occupancy') }}">{{ __('Occupancy') }}</a>
                            </li>
                            <li>
                                <a href="{{ route('owner.reports.maintenance') }}">{{ __('Maintenance') }}</a>
                            </li>
                            <li>
                                <a href="{{ route('owner.reports.tenant') }}">{{ __('Tenant') }}</a>
                            </li>
                        </ul>
                    </li>
                @endcan
                @can('Manage Settings')
                    <li>
                        <a href="{{ route('owner.setting.gateway.index') }}">
                            <i class="ri-settings-3-line"></i>
                            <span>{{ __('Settings') }}</span>
                        </a>
                    </li>
                @endcan
                @can('Manage Team')
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i class="ri-team-line"></i>
                            <span>{{ __('Manage Staff') }}</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="{{ route('owner.role-permission.role-list') }}">{{ __('Role & Permission') }}</a>
                            </li>
                            <li>
                                <a href="{{ route('owner.team-member.index') }}">{{ __('Staff Users') }}</a>
                            </li>
                        </ul>
                    </li>
                @endcan
                @if (isAddonInstalled('PROTYSMS') > 0)
                    @can('Manage Bulk SMS/Mail')
                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i class="ri-mail-send-line"></i>
                                <span>{{ __('Bulk Sms/Mail') }}</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li>
                                    <a href="{{ route('sms-mail.sms') }}">{{ __('Sms') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('sms-mail.mail') }}">{{ __('Email') }}</a>
                                </li>
                                @if (isAddonInstalled('PROTYSMS') > 2)
                                    <li>
                                        <a href="{{ route('sms-mail.template.index') }}">{{ __('Email Template') }}</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endcan
                @endif
                @if (isAddonInstalled('PROTYAGREEMENT') > 0)
                    @can('Manage Agreement')
                        <li>
                            <a href="{{ route('owner.agreement.index') }}">
                                <i class="ri-quill-pen-line"></i>
                                <span>{{ __('Agreement') }}</span>
                            </a>
                        </li>
                    @endcan
                @endif
                @if (isAddonInstalled('PROTYTENANCY') > 0)
                    @if (env('TENANCY_STATUS') == ACTIVE)
                        @can('Manage Domain Config')
                            <li>
                                <a href="{{ route('owner.domain.index') }}">
                                    <i class="ri-global-line"></i>
                                    <span>{{ __('Domain Config') }}</span>
                                </a>
                            </li>
                        @endcan
                    @endif
                @endif
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
                @if (isAddonInstalled('PROTYSAAS') > 1)
                    @can('Manage Subscription')
                        <li>
                            <a href="{{ route('owner.subscription.index') }}">
                                <i class="ri-vip-crown-2-line"></i>
                                <span>{{ __('My Subscription') }}</span>
                            </a>
                        </li>
                    @endcan
                @endif
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
