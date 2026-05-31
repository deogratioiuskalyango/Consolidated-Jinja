<?php

namespace Botble\Gallery\Http\Controllers;

use Botble\Gallery\Facades\Gallery;
use Botble\Gallery\Models\Gallery as GalleryModel;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Routing\Controller;

class PublicController extends Controller
{
    public function getGalleries()
    {
        Gallery::registerAssets();

        $galleries = GalleryModel::query()
            ->wherePublished()
            ->with(['slugable', 'user'])
            ->orderBy('order')
            ->orderByDesc('created_at')
            ->get();

        SeoHelper::setTitle(__('Galleries'));

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Galleries'), Gallery::getGalleriesPageUrl());

        return Theme::scope('galleries', compact('galleries'), 'plugins/gallery::themes.galleries')
            ->render();
    }
}
