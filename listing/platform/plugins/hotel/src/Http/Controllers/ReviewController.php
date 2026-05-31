<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Models\Review;
use Botble\Hotel\Tables\ReviewTable;
use Exception;
use Illuminate\Http\Request;

class ReviewController extends BaseController
{
    public function index(ReviewTable $dataTable)
    {
        PageTitle::setTitle(trans('plugins/hotel::review.name'));

        Assets::addStylesDirectly('vendor/core/plugins/hotel/css/review.css');

        return $dataTable->renderTable();
    }

    public function destroy(int|string $id, Request $request, BaseHttpResponse $response)
    {
        try {
            $review = Review::query()->findOrFail($id);
            $review->delete();

            event(new DeletedContentEvent(REVIEW_MODULE_SCREEN_NAME, $request, $review));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
