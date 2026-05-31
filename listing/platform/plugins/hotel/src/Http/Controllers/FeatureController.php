<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Forms\FeatureForm;
use Botble\Hotel\Http\Requests\FeatureRequest;
use Botble\Hotel\Models\Feature;
use Botble\Hotel\Tables\FeatureTable;
use Exception;
use Illuminate\Http\Request;

class FeatureController extends BaseController
{
    public function index(FeatureTable $table)
    {
        PageTitle::setTitle(trans('plugins/hotel::feature.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/hotel::feature.create'));

        return $formBuilder->create(FeatureForm::class)->renderForm();
    }

    public function store(FeatureRequest $request, BaseHttpResponse $response)
    {
        $feature = Feature::query()->create($request->input());

        event(new CreatedContentEvent(FEATURE_MODULE_SCREEN_NAME, $request, $feature));

        return $response
            ->setPreviousUrl(route('feature.index'))
            ->setNextUrl(route('feature.edit', $feature->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Feature $feature, FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $feature->name]));

        return $formBuilder->create(FeatureForm::class, ['model' => $feature])->renderForm();
    }

    public function update(Feature $feature, FeatureRequest $request, BaseHttpResponse $response)
    {
        $feature->fill($request->input());
        $feature->save();

        event(new UpdatedContentEvent(FEATURE_MODULE_SCREEN_NAME, $request, $feature));

        return $response
            ->setPreviousUrl(route('feature.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Feature $feature, Request $request, BaseHttpResponse $response)
    {
        try {
            $feature->delete();

            event(new DeletedContentEvent(FEATURE_MODULE_SCREEN_NAME, $request, $feature));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
