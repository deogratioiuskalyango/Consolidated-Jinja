@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row">
        <div class="col-lg-12">
            {!! do_action('booking_reports_before_component_render') !!}

            <calendar-booking-reports-component
                v-cloak
                events-url="{{ route('booking.reports.records.index') }}"
            >
                <template v-slot:title>
                    {{ trans('plugins/hotel::booking.calendar') }}
                </template>

                <template v-slot:event="{ booking }">
                    <x-core::modal
                        id="view-booking-event"
                        type="info"
                        v-if="booking"
                        :title="trans('plugins/hotel::booking.name')"
                        size="modal-lg"
                    >
                        <div v-html="booking"></div>
                        <x-slot name="footer">
                            <button
                                class="float-end btn btn-secondary"
                                data-bs-dismiss="modal"
                                type="button"
                            >{{ trans('core/base::forms.cancel') }}</button>
                            <a
                                id="view-booking-event-link"
                                href="#"
                                target="_blank"
                            >
                                <button
                                    class="float-end btn btn-primary"
                                    type="button"
                                >{{ trans('core/base::forms.edit') }}</button>
                            </a>
                        </x-slot>
                    </x-core::modal>
                </template>

                <template v-slot:loading>
                    @include('core/base::elements.loading')
                </template>
            </calendar-booking-reports-component>

            {!! do_action('booking_reports_after_component_render') !!}
        </div>
    </div>
@endsection
