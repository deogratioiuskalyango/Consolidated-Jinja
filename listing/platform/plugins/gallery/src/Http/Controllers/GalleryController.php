<?php

namespace Botble\Gallery\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Gallery\Forms\GalleryForm;
use Botble\Gallery\Http\Requests\GalleryRequest;
use Botble\Gallery\Models\Gallery;
use Botble\Gallery\Tables\GalleryTable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GalleryController extends BaseController
{
    public function index(GalleryTable $dataTable)
    {
        PageTitle::setTitle(trans('plugins/gallery::gallery.galleries'));

        return $dataTable->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/gallery::gallery.create'));

        return $formBuilder->create(GalleryForm::class)->renderForm();
    }

    public function store(GalleryRequest $request, BaseHttpResponse $response)
    {
        $gallery = new Gallery();
        $gallery->fill($request->input());
        $gallery->user_id = Auth::guard()->id();
        $gallery->save();

        event(new CreatedContentEvent(GALLERY_MODULE_SCREEN_NAME, $request, $gallery));

        return $response
            ->setPreviousUrl(route('galleries.index'))
            ->setNextUrl(route('galleries.edit', $gallery->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Gallery $gallery, FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $gallery->name]));

        return $formBuilder->create(GalleryForm::class, ['model' => $gallery])->renderForm();
    }

    public function update(Gallery $gallery, GalleryRequest $request, BaseHttpResponse $response)
    {
        $gallery->fill($request->input());
        $gallery->save();

        event(new UpdatedContentEvent(GALLERY_MODULE_SCREEN_NAME, $request, $gallery));

        return $response
            ->setPreviousUrl(route('galleries.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Gallery $gallery, Request $request, BaseHttpResponse $response)
    {
        try {
            $gallery->delete();
            event(new DeletedContentEvent(GALLERY_MODULE_SCREEN_NAME, $request, $gallery));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
