<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Forms\FoodForm;
use Botble\Hotel\Http\Requests\FoodRequest;
use Botble\Hotel\Models\Food;
use Botble\Hotel\Tables\FoodTable;
use Exception;
use Illuminate\Http\Request;

class FoodController extends BaseController
{
    public function index(FoodTable $table)
    {
        PageTitle::setTitle(trans('plugins/hotel::food.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/hotel::food.create'));

        return $formBuilder->create(FoodForm::class)->renderForm();
    }

    public function store(FoodRequest $request, BaseHttpResponse $response)
    {
        $food = Food::query()->create($request->input());

        event(new CreatedContentEvent(FOOD_MODULE_SCREEN_NAME, $request, $food));

        return $response
            ->setPreviousUrl(route('food.index'))
            ->setNextUrl(route('food.edit', $food->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Food $food, FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $food->name]));

        return $formBuilder->create(FoodForm::class, ['model' => $food])->renderForm();
    }

    public function update(Food $food, FoodRequest $request, BaseHttpResponse $response)
    {
        $food->fill($request->input());

        $food->save();

        event(new UpdatedContentEvent(FOOD_MODULE_SCREEN_NAME, $request, $food));

        return $response
            ->setPreviousUrl(route('food.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Food $food, Request $request, BaseHttpResponse $response)
    {
        try {
            $food->delete();

            event(new DeletedContentEvent(FOOD_MODULE_SCREEN_NAME, $request, $food));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
