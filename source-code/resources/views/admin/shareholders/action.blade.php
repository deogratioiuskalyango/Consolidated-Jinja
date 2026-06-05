<div class="tbl-action-btns d-inline-flex">
    <button type="button"
            class="p-1 tbl-action-btn text-primary sh-edit-btn"
            data-id="{{ $shareholder->id }}"
            data-url="{{ route('admin.shareholders.edit', $shareholder->id) }}"
            title="{{ __('Edit') }}">
        <span class="iconify" data-icon="mdi:pencil-outline"></span>
    </button>
    <button type="button"
            class="p-1 tbl-action-btn text-secondary sh-roles-btn"
            data-id="{{ $shareholder->id }}"
            data-name="{{ $shareholder->user->first_name ?? '' }} {{ $shareholder->user->last_name ?? '' }}"
            title="{{ __('Manage Roles') }}">
        <span class="iconify" data-icon="mdi:account-multiple-check-outline"></span>
    </button>
    <button type="button"
            class="p-1 tbl-action-btn text-info sh-resend-btn"
            data-id="{{ $shareholder->id }}"
            data-name="{{ $shareholder->user->first_name ?? '' }} {{ $shareholder->user->last_name ?? '' }}"
            data-url="{{ route('admin.shareholders.resend-credentials', $shareholder->id) }}"
            title="{{ __('Resend Credentials') }}">
        <span class="iconify" data-icon="mdi:email-send-outline"></span>
    </button>
    <button type="button"
            class="p-1 tbl-action-btn text-danger sh-delete-btn"
            data-id="{{ $shareholder->id }}"
            data-name="{{ $shareholder->user->first_name ?? '' }} {{ $shareholder->user->last_name ?? '' }}"
            title="{{ __('Delete') }}">
        <span class="iconify" data-icon="mdi:trash-can-outline"></span>
    </button>
    @if($shareholder->status == SHAREHOLDER_STATUS_ACTIVE)
        <button type="button"
                class="p-1 tbl-action-btn text-warning sh-suspend-btn"
                data-id="{{ $shareholder->id }}"
                title="{{ __('Suspend') }}">
            <span class="iconify" data-icon="mdi:account-cancel-outline"></span>
        </button>
    @else
        <button type="button"
                class="p-1 tbl-action-btn text-success sh-reactivate-btn"
                data-id="{{ $shareholder->id }}"
                title="{{ __('Reactivate') }}">
            <span class="iconify" data-icon="mdi:account-check-outline"></span>
        </button>
    @endif
</div>
