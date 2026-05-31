<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Forms\PlaceForm;
use Botble\Hotel\Http\Requests\PlaceRequest;
use Botble\Hotel\Models\Place;
use Botble\Hotel\Tables\PlaceTable;
use Exception;
use Illuminate\Http\Request;

class PlaceController extends BaseController
{
    public function index(PlaceTable $table)
    {
        PageTitle::setTitle(trans('plugins/hotel::place.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/hotel::place.create'));

        return $formBuilder->create(PlaceForm::class)->renderForm();
    }

    public function store(PlaceRequest $request, BaseHttpResponse $response)
    {
        $place = Place::query()->create($request->input());

        event(new CreatedContentEvent(PLACE_MODULE_SCREEN_NAME, $request, $place));

        return $response
            ->setPreviousUrl(route('place.index'))
            ->setNextUrl(route('place.edit', $place->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Place $place, FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $place->name]));

        return $formBuilder->create(PlaceForm::class, ['model' => $place])->renderForm();
    }

    public function update(Place $place, PlaceRequest $request, BaseHttpResponse $response)
    {
        $place->fill($request->input());

        $place->save();

        event(new UpdatedContentEvent(PLACE_MODULE_SCREEN_NAME, $request, $place));

        return $response
            ->setPreviousUrl(route('place.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Place $place, Request $request, BaseHttpResponse $response)
    {
        try {
            $place->delete();

            event(new DeletedContentEvent(PLACE_MODULE_SCREEN_NAME, $request, $place));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
