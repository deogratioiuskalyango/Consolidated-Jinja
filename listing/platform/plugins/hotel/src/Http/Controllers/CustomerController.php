<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Forms\CustomerForm;
use Botble\Hotel\Http\Requests\CustomerCreateRequest;
use Botble\Hotel\Http\Requests\CustomerEditRequest;
use Botble\Hotel\Models\Customer;
use Botble\Hotel\Tables\CustomerTable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends BaseController
{
    public function index(CustomerTable $table)
    {
        PageTitle::setTitle(trans('plugins/hotel::customer.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/hotel::customer.create'));

        return $formBuilder->create(CustomerForm::class)->renderForm();
    }

    public function store(CustomerCreateRequest $request, BaseHttpResponse $response)
    {
        $customer = new Customer($request->input());
        $customer->password = Hash::make($request->input('password'));
        $customer->save();

        event(new CreatedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));

        return $response
            ->setPreviousUrl(route('customer.index'))
            ->setNextUrl(route('customer.edit', $customer->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Customer $customer, FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $customer->name]));

        Assets::addScriptsDirectly('vendor/core/plugins/hotel/js/customer.js');

        $customer->password = null;

        return $formBuilder->create(CustomerForm::class, ['model' => $customer])->renderForm();
    }

    public function update(Customer $customer, CustomerEditRequest $request, BaseHttpResponse $response)
    {
        $customer->fill($request->except('password'));

        if ($request->input('is_change_password') == 1) {
            $customer->password = Hash::make($request->input('password'));
        }

        $customer->save();

        event(new UpdatedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));

        return $response
            ->setPreviousUrl(route('customer.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Customer $customer, Request $request, BaseHttpResponse $response)
    {
        try {
            $customer->delete();

            event(new DeletedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
