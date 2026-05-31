<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Facades\InvoiceHelper;
use Botble\Hotel\Models\Invoice;
use Botble\Hotel\Tables\InvoiceTable;
use Exception;
use Illuminate\Http\Request;

class InvoiceController extends BaseController
{
    public function index(InvoiceTable $table)
    {
        PageTitle::setTitle(trans('plugins/hotel::invoice.name'));

        Assets::addScripts(['bootstrap-editable'])
            ->addStyles(['bootstrap-editable']);

        return $table->renderTable();
    }

    public function show(int|string $id)
    {
        $invoice = Invoice::query()->findOrFail($id);

        PageTitle::setTitle(trans('plugins/hotel::invoice.show', ['code' => $invoice->code]));

        return view('plugins/hotel::invoices.show', compact('invoice'));
    }

    public function destroy(int|string $id, Request $request, BaseHttpResponse $response)
    {
        $invoice = Invoice::query()->findOrFail($id);

        try {
            $invoice->delete();
            event(new DeletedContentEvent(INVOICE_MODULE_SCREEN_NAME, $request, $invoice));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function getGenerateInvoice(int|string $id, Request $request)
    {
        $invoice = Invoice::query()->findOrFail($id);

        if ($request->input('type') === 'print') {
            return InvoiceHelper::streamInvoice($invoice);
        }

        return InvoiceHelper::downloadInvoice($invoice);
    }
}
