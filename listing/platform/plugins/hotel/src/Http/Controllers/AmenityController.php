<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Forms\AmenityForm;
use Botble\Hotel\Http\Requests\AmenityRequest;
use Botble\Hotel\Models\Amenity;
use Botble\Hotel\Tables\AmenityTable;
use Exception;
use Illuminate\Http\Request;

class AmenityController extends BaseController
{
    public function index(AmenityTable $table)
    {
        PageTitle::setTitle(trans('plugins/hotel::amenity.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/hotel::amenity.create'));

        return $formBuilder->create(AmenityForm::class)->renderForm();
    }

    public function store(AmenityRequest $request, BaseHttpResponse $response)
    {
        $amenity = Amenity::query()->create($request->input());

        event(new CreatedContentEvent(AMENITIES_MODULE_SCREEN_NAME, $request, $amenity));

        return $response
            ->setPreviousUrl(route('amenity.index'))
            ->setNextUrl(route('amenity.edit', $amenity->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Amenity $amenity, FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $amenity->name]));

        return $formBuilder->create(AmenityForm::class, ['model' => $amenity])->renderForm();
    }

    public function update(Amenity $amenity, AmenityRequest $request, BaseHttpResponse $response)
    {
        $amenity->fill($request->input());
        $amenity->save();

        event(new UpdatedContentEvent(AMENITIES_MODULE_SCREEN_NAME, $request, $amenity));

        return $response
            ->setPreviousUrl(route('amenity.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Amenity $amenity, Request $request, BaseHttpResponse $response)
    {
        try {
            $amenity->delete();

            event(new DeletedContentEvent(AMENITIES_MODULE_SCREEN_NAME, $request, $amenity));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
