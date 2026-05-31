<script>
export default {
    props: {
        eventsUrl: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            calendarInstance: null,
            booking: null,
            loading: true,
        }
    },

    async mounted() {
        await this.$nextTick()

        if (this.calendarInstance) {
            calendarInstance.destroy()
        }

        if (this.$refs.calendar) {
            this.calendarInstance = new FullCalendar.Calendar(this.$refs.calendar, {
                fixedWeekCount: false,
                headerToolbar: {
                    left: 'title',
                },
                navLinks: true,
                editable: false,
                dayMaxEvents: true,
                events: {
                    url: this.eventsUrl,
                },
                loading: (isLoading) => {
                    this.loading = isLoading
                },
                eventClick: (info) => {
                    this.booking = info.event.extendedProps.detail
                    $('#view-booking-event-link').attr('href', info.event.extendedProps.detailUrl)
                    $('#view-booking-event').modal('show')
                },
            })

            this.calendarInstance.render()
        }
    },
}
</script>

<template>
    <div class="widget meta-boxes">
        <div class="widget-title">
            <slot name="title"></slot>
        </div>

        <div class="widget-body" ref="calendar"></div>

        <div v-if="loading" class="loading">
            <slot name="loading"></slot>
        </div>

        <slot name="event" :booking="booking"></slot>
    </div>
</template>

<style scoped>
.widget {
    position: relative;
}

.widget .loading {
    position: absolute;
    inset: 0;
    background-color: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
}
</style>
