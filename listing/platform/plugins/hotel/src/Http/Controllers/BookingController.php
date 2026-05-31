<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Forms\BookingForm;
use Botble\Hotel\Http\Requests\UpdateBookingRequest;
use Botble\Hotel\Models\Booking;
use Botble\Hotel\Tables\BookingTable;
use Exception;
use Illuminate\Http\Request;

class BookingController extends BaseController
{
    public function index(BookingTable $table)
    {
        PageTitle::setTitle(trans('plugins/hotel::booking.name'));

        return $table->renderTable();
    }

    public function edit(Booking $booking, FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $booking->room->room_name]));

        return $formBuilder->create(BookingForm::class, ['model' => $booking])->renderForm();
    }

    public function update(Booking $booking, UpdateBookingRequest $request, BaseHttpResponse $response)
    {
        $booking->fill($request->input());
        $booking->save();

        event(new UpdatedContentEvent(BOOKING_MODULE_SCREEN_NAME, $request, $booking));

        return $response
            ->setPreviousUrl(route('booking.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Booking $booking, Request $request, BaseHttpResponse $response)
    {
        try {
            $booking->delete();

            event(new DeletedContentEvent(BOOKING_MODULE_SCREEN_NAME, $request, $booking));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
