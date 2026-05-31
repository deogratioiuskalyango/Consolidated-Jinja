<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Forms\RoomCategoryForm;
use Botble\Hotel\Http\Requests\RoomCategoryRequest;
use Botble\Hotel\Models\RoomCategory;
use Botble\Hotel\Tables\RoomCategoryTable;
use Exception;
use Illuminate\Http\Request;

class RoomCategoryController extends BaseController
{
    public function index(RoomCategoryTable $table)
    {
        PageTitle::setTitle(trans('plugins/hotel::room-category.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/hotel::room-category.create'));

        return $formBuilder->create(RoomCategoryForm::class)->renderForm();
    }

    public function store(RoomCategoryRequest $request, BaseHttpResponse $response)
    {
        $roomCategory = RoomCategory::query()->create($request->input());

        event(new CreatedContentEvent(ROOM_CATEGORY_MODULE_SCREEN_NAME, $request, $roomCategory));

        return $response
            ->setPreviousUrl(route('room-category.index'))
            ->setNextUrl(route('room-category.edit', $roomCategory->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(RoomCategory $roomCategory, FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $roomCategory->name]));

        return $formBuilder->create(RoomCategoryForm::class, ['model' => $roomCategory])->renderForm();
    }

    public function update(RoomCategory $roomCategory, RoomCategoryRequest $request, BaseHttpResponse $response)
    {
        $roomCategory->fill($request->input());

        $roomCategory->save();

        event(new UpdatedContentEvent(ROOM_CATEGORY_MODULE_SCREEN_NAME, $request, $roomCategory));

        return $response
            ->setPreviousUrl(route('room-category.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(RoomCategory $roomCategory, Request $request, BaseHttpResponse $response)
    {
        try {
            $roomCategory->delete();

            event(new DeletedContentEvent(ROOM_CATEGORY_MODULE_SCREEN_NAME, $request, $roomCategory));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
