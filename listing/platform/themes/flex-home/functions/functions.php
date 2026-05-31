<?php

use Botble\Base\Facades\MacroableModels;
use Botble\Base\Facades\MetaBox;
use Botble\Base\Forms\FieldOptions\DatePickerFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\Fields\DatePickerField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\FormAbstract;
use Botble\Media\Facades\RvMedia;
use Botble\Newsletter\Facades\Newsletter;
use Botble\Page\Forms\PageForm;
use Botble\RealEstate\Http\Requests\ProjectRequest;
use Botble\RealEstate\Http\Requests\PropertyRequest;
use Botble\RealEstate\Models\Facility;
use Botble\RealEstate\Models\Feature;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Botble\Support\Http\Requests\Request;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Forms\Fields\ThemeIconField;
use Botble\Theme\Supports\ThemeSupport;
use Botble\Theme\Supports\Youtube;
use Botble\Theme\Typography\TypographyItem;
use Illuminate\Support\Arr;

register_page_template([
    'homepage' => __('Homepage'),
    'full-width' => __('Full width'),
]);

register_sidebar([
    'id' => 'footer_sidebar',
    'name' => __('Footer sidebar'),
    'description' => __('Footer sidebar for Flex Home theme'),
]);

RvMedia::setUploadPathAndURLToPublic()
    ->addSize('small', 410, 270)
    ->addSize('medium', 600, 600);

function get_object_property_map(): object
{
    return (object) [
        'name' => '__name__',
        'status_html' => '__status_html__',
        'url' => '__url__',
        'city_name' => '__city_name__',
        'square_text' => '__square_text__',
        'number_bedroom' => '__number_bedroom__',
        'number_bathroom' => '__number_bathroom__',
        'image_thumb' => '__image_thumb__',
        'price' => '__price__',
        'price_html' => '__price_html__',
    ];
}

app()->booted(function (): void {
    ThemeSupport::registerToastNotification();
    ThemeSupport::registerPreloader();
    ThemeSupport::registerSocialSharing();
    ThemeSupport::registerSiteCopyright();

    if (is_plugin_active('newsletter')) {
        Newsletter::registerNewsletterPopup();
    }

    add_filter('ads_locations', function (array $locations) {
        return [
            ...$locations,
            'header_before' => __('Header (before)'),
            'header_after' => __('Header (after)'),
            'footer_before' => __('Footer (before)'),
            'footer_after' => __('Footer (after)'),
            'project_list_before' => __('Project List (before)'),
            'project_list_after' => __('Project List (after)'),
            'project_detail_before' => __('Project Detail (before)'),
            'project_detail_after' => __('Project Detail (after)'),
            'project_detail_sidebar_before' => __('Project Detail Sidebar (before)'),
            'project_detail_sidebar_after' => __('Project Detail Sidebar (after)'),
            'property_list_before' => __('Property List (before)'),
            'property_list_after' => __('Property List (after)'),
            'property_detail_before' => __('Property Detail (before)'),
            'property_detail_after' => __('Property Detail (after)'),
            'property_detail_sidebar_before' => __('Property Detail Sidebar (before)'),
            'property_detail_sidebar_after' => __('Property Detail Sidebar (after)'),
            'career_list_before' => __('Career List (before)'),
            'career_list_after' => __('Career List (after)'),
            'career_detail_before' => __('Career Detail (before)'),
            'career_detail_after' => __('Career Detail (after)'),
            'career_sidebar_before' => __('Career Detail Sidebar (before)'),
            'career_sidebar_after' => __('Career Detail Sidebar (after)'),
            'blog_list_before' => __('Blog List (before)'),
            'blog_list_after' => __('Blog List (after)'),
            'blog_sidebar_before' => __('Blog Sidebar (before)'),
            'blog_sidebar_after' => __('Blog Sidebar (after)'),
            'post_detail_before' => __('Post Detail (before)'),
            'post_detail_after' => __('Post Detail (after)'),
        ];
    }, 999);

    Theme::typography()
        ->registerFontFamilies([
            new TypographyItem('primary', __('Primary'), theme_option('primary_font', 'Nunito Sans')),
        ]);

    if (is_plugin_active('real-estate')) {
        add_filter(BASE_FILTER_BEFORE_RENDER_FORM, function (FormAbstract $form, $data): FormAbstract {
            switch ($data::class) {
                case Facility::class:
                case Feature::class:

                    $iconImage = null;
                    if ($data->getKey()) {
                        $iconImage = MetaBox::getMetaData($data, 'icon_image', true);
                    }

                    $form
                        ->modify('icon', ThemeIconField::class, ['label' => __('Font Icon')], true)
                        ->addAfter('icon', 'icon_image', MediaImageField::class, [
                            'value' => $iconImage,
                            'label' => __('Icon Image (It will replace Font Icon if it is present)'),
                        ]);

                    break;
                case Property::class:
                    $form->addAfter(
                        'unique_id',
                        'last_updated',
                        DatePickerField::class,
                        DatePickerFieldOption::make()->label(__('Last Updated'))->metadata()->toArray()
                    );

                    break;

                case Project::class:
                    $form->addAfter(
                        'date_sell',
                        'last_updated',
                        DatePickerField::class,
                        DatePickerFieldOption::make()->label(__('Last Updated'))->metadata()->toArray()
                    );

                    break;
            }

            return $form;
        }, 127, 2);

        $videoSupportModels = [Project::class, Property::class];

        add_action(BASE_ACTION_META_BOXES, function ($context, $object) use ($videoSupportModels): void {
            if (in_array(get_class($object), $videoSupportModels) && $context == 'advanced') {
                MetaBox::addMetaBox('additional_property_fields', __('Addition Information'), function () {
                    $videoThumbnail = null;
                    $videoUrl = null;
                    $args = func_get_args();
                    if (! empty($args[0])) {
                        $videoThumbnail = $args[0]->video_thumbnail;
                        $videoUrl = $args[0]->video_url;
                    }

                    return Theme::partial('additional-property-fields', compact('videoThumbnail', 'videoUrl'));
                }, get_class($object), $context);
            }
        }, 28, 2);

        add_action([BASE_ACTION_AFTER_CREATE_CONTENT, BASE_ACTION_AFTER_UPDATE_CONTENT], function ($type, $request, $object) use ($videoSupportModels): void {
            if (in_array($object::class, $videoSupportModels) && $request->has('video')) {
                $data = Arr::only((array) $request->input('video', []), ['url', 'thumbnail']);

                if ($request->hasFile('thumbnail_input')) {
                    $result = RvMedia::handleUpload($request->file('thumbnail_input'), 0, 'properties');
                    if (! $result['error']) {
                        $file = $result['data'];
                        $data['thumbnail'] = $file->url;
                    }
                }

                MetaBox::saveMetaBoxData($object, 'video', $data);
            }

            if ($request->has('last_updated')) {
                MetaBox::saveMetaBoxData($object, 'last_updated', $request->input('last_updated'));
            }
        }, 280, 3);

        add_filter('core_request_rules', function (array $rules, Request $request) {
            if ($request instanceof PropertyRequest || $request instanceof ProjectRequest) {
                $rules['video.thumbnail'] = 'nullable|string|max:400';
                $rules['video.url'] = 'nullable|string|max:400';
                $rules['last_updated'] = 'nullable|date';
            }

            return $rules;
        }, 120, 2);

        foreach ($videoSupportModels as $supportModel) {
            MacroableModels::addMacro($supportModel, 'getVideoThumbnailAttribute', function () {
                $this->loadMissing('metadata');

                return Arr::get($this->getMetaData('video', true) ?: [], 'thumbnail', '');
            });

            MacroableModels::addMacro($supportModel, 'getVideoUrlAttribute', function () {
                $this->loadMissing('metadata');

                return Arr::get($this->getMetaData('video', true) ?: [], 'url', '');
            });
        }
    }

    PageForm::extend(function (PageForm $form): void {
        $form
            ->add(
                'breadcrumb',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Breadcrumb'))
                    ->metadata()
                    ->choices([
                        'yes' => __('Yes'),
                        'no' => __('No'),
                    ]),
            );
    });
});

function get_image_from_video_property(Property|Project $item): string
{
    if ($item->video_thumbnail) {
        return RvMedia::getImageUrl($item->video_thumbnail);
    }

    $videoID = Youtube::getYoutubeVideoID($item->video_url);

    if ($videoID) {
        return 'https://img.youtube.com/vi/' . $videoID . '/hqdefault.jpg';
    }

    return RvMedia::getDefaultImage();
}

if (is_plugin_active('real-estate')) {
    add_action([BASE_ACTION_AFTER_CREATE_CONTENT, BASE_ACTION_AFTER_UPDATE_CONTENT], function ($type, $request, $object): void {
        if (in_array(get_class($object), [Facility::class, Feature::class]) && $request->has('icon_image')) {
            MetaBox::saveMetaBoxData($object, 'icon_image', $request->input('icon_image'));
        }
    }, 230, 3);
}
