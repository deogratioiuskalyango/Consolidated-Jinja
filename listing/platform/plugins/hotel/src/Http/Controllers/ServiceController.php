<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Forms\ServiceForm;
use Botble\Hotel\Http\Requests\ServiceRequest;
use Botble\Hotel\Models\Service;
use Botble\Hotel\Tables\ServiceTable;
use Exception;
use Illuminate\Http\Request;

class ServiceController extends BaseController
{
    public function index(ServiceTable $table)
    {
        PageTitle::setTitle(trans('plugins/hotel::service.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/hotel::service.create'));

        return $formBuilder->create(ServiceForm::class)->renderForm();
    }

    public function store(ServiceRequest $request, BaseHttpResponse $response)
    {
        $service = Service::query()->create($request->input());

        event(new CreatedContentEvent(SERVICE_MODULE_SCREEN_NAME, $request, $service));

        return $response
            ->setPreviousUrl(route('service.index'))
            ->setNextUrl(route('service.edit', $service->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Service $service, FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $service->name]));

        return $formBuilder->create(ServiceForm::class, ['model' => $service])->renderForm();
    }

    public function update(Service $service, ServiceRequest $request, BaseHttpResponse $response)
    {
        $service->fill($request->input());

        $service->save();

        event(new UpdatedContentEvent(SERVICE_MODULE_SCREEN_NAME, $request, $service));

        return $response
            ->setPreviousUrl(route('service.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Service $service, Request $request, BaseHttpResponse $response)
    {
        try {
            $service->delete();

            event(new DeletedContentEvent(SERVICE_MODULE_SCREEN_NAME, $request, $service));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
