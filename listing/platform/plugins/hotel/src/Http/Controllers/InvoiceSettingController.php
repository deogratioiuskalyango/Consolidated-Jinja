<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Http\Requests\UpdateInvoiceSettingsRequest;
use Botble\Setting\Supports\SettingStore;

class InvoiceSettingController extends BaseController
{
    public function getSettings()
    {
        PageTitle::setTitle(trans('plugins/hotel::hotel.settings'));

        return view('plugins/hotel::settings.invoice-settings');
    }

    public function postSettings(
        UpdateInvoiceSettingsRequest $request,
        BaseHttpResponse $response,
        SettingStore $settingStore
    ) {
        $settingStore->set($request->validated());

        $settingStore->save();

        return $response
            ->setNextUrl(route('hotel.invoice.settings'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }
}
