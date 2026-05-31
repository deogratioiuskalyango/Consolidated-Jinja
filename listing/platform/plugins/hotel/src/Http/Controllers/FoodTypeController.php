<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Forms\FoodTypeForm;
use Botble\Hotel\Http\Requests\FoodTypeRequest;
use Botble\Hotel\Models\FoodType;
use Botble\Hotel\Tables\FoodTypeTable;
use Exception;
use Illuminate\Http\Request;

class FoodTypeController extends BaseController
{
    public function index(FoodTypeTable $table)
    {
        PageTitle::setTitle(trans('plugins/hotel::food-type.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/hotel::food-type.create'));

        return $formBuilder->create(FoodTypeForm::class)->renderForm();
    }

    public function store(FoodTypeRequest $request, BaseHttpResponse $response)
    {
        $foodType = FoodType::query()->create($request->input());

        event(new CreatedContentEvent(FOOD_TYPE_MODULE_SCREEN_NAME, $request, $foodType));

        return $response
            ->setPreviousUrl(route('food-type.index'))
            ->setNextUrl(route('food-type.edit', $foodType->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(FoodType $foodType, FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $foodType->name]));

        return $formBuilder->create(FoodTypeForm::class, ['model' => $foodType])->renderForm();
    }

    public function update(FoodType $foodType, FoodTypeRequest $request, BaseHttpResponse $response)
    {
        $foodType->fill($request->input());

        $foodType->save();

        event(new UpdatedContentEvent(FOOD_TYPE_MODULE_SCREEN_NAME, $request, $foodType));

        return $response
            ->setPreviousUrl(route('food-type.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(FoodType $foodType, Request $request, BaseHttpResponse $response)
    {
        try {
            $foodType->delete();

            event(new DeletedContentEvent(FOOD_TYPE_MODULE_SCREEN_NAME, $request, $foodType));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
