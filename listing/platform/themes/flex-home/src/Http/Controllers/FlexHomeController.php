<?php

namespace Theme\FlexHome\Http\Controllers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Location\Repositories\Interfaces\CityInterface;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Http\Controllers\PublicController;
use Illuminate\Http\Request;
use Theme\FlexHome\Http\Resources\PropertyResource;

class FlexHomeController extends PublicController
{
    public function ajaxGetPropertiesForMap(Request $request, BaseHttpResponse $response)
    {
        $filters = [
            'keyword' => $request->input('k'),
            'type' => $request->input('type'),
            'bedroom' => $request->input('bedroom'),
            'bathroom' => $request->input('bathroom'),
            'floor' => $request->input('floor'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'min_square' => $request->input('min_square'),
            'max_square' => $request->input('max_square'),
            'project' => $request->input('project'),
            'category_id' => $request->input('category_id'),
            'city' => $request->input('city'),
            'city_id' => $request->input('city_id'),
            'location' => $request->input('location'),
        ];

        array_map(function ($value) {
            return urldecode($value);
        }, $filters);

        $params = [
            'with' => RealEstateHelper::getPropertyRelationsQuery(),
            'paginate' => [
                'per_page' => 20,
                'current_paged' => $request->integer('page', 1),
            ],
        ];

        $properties = app(PropertyInterface::class)->getProperties($filters, $params);

        return $response
            ->setData(PropertyResource::collection($properties))
            ->toApiResponse();
    }

    public function ajaxGetCities(Request $request, CityInterface $cityRepository, BaseHttpResponse $response)
    {
        abort_unless($request->ajax(), 404);

        $keyword = $request->input('k');

        $cities = $cityRepository->filters($keyword);

        return $response->setData(Theme::partial('city-suggestion', ['items' => $cities]));
    }

    public function getWishlist(Request $request, PropertyInterface $propertyRepository, ProjectInterface $projectRepository)
    {
        abort_unless(RealEstateHelper::isEnabledWishlist(), 404);

        SeoHelper::setTitle(__('Wishlist'))
            ->setDescription(__('Wishlist'));

        $propertyCookieName = 'wishlist';
        $propertyJsonWishlist = $_COOKIE[$propertyCookieName] ?? null;
        $projectCookieName = 'project_wishlist';
        $projectJsonWishlist = $_COOKIE[$projectCookieName] ?? null;

        $properties = collect();
        $projects = collect();

        if (! empty($propertyJsonWishlist)) {
            $arrValue = collect(json_decode($propertyJsonWishlist, true))->flatten()->all();
            $properties = $propertyRepository->advancedGet([
                'condition' => [
                    ['re_properties.id', 'IN', $arrValue],
                ],
                'order_by' => [
                    're_properties.id' => 'DESC',
                ],
                'paginate' => [
                    'per_page' => (int) theme_option('number_of_properties_per_page', 12),
                    'page_name' => 'properties',
                    'current_paged' => $request->integer('properties', 1),
                ],
                'with' => RealEstateHelper::getPropertyRelationsQuery(),
            ]);
        }

        if (! empty($projectJsonWishlist)) {
            $arrValue = collect(json_decode($projectJsonWishlist, true))->flatten()->all();
            $projects = $projectRepository->advancedGet([
                'condition' => [
                    ['re_projects.id', 'IN', $arrValue],
                ],
                'order_by' => [
                    're_projects.id' => 'DESC',
                ],
                'paginate' => [
                    'per_page' => (int) theme_option('number_of_properties_per_page', 12),
                    'page_name' => 'projects',
                    'current_paged' => $request->integer('projects', 1),
                ],
                'with' => RealEstateHelper::getProjectRelationsQuery(),
            ]);
        }

        Theme::breadcrumb()
            ->add(__('Wishlist'));

        return Theme::scope('real-estate.wishlist', compact('properties', 'projects'))->render();
    }

    public function ajaxGetProjectsFilter(
        Request $request,
        BaseHttpResponse $response,
        ProjectInterface $projectRepository
    ) {
        abort_unless($request->ajax(), 404);

        $request->validate([
            'project' => 'nullable|string',
        ]);

        $keyword = BaseHelper::clean($request->input('project'));

        $projects = $projectRepository->advancedGet([
            'condition' => [
                ['name', 'LIKE', '%' . $keyword . '%'],
            ],
            'select' => ['id', 'name'],
            'take' => 10,
            'order_by' => ['name' => 'ASC'],
        ]);

        return $response->setData(Theme::partial('real-estate.filters.projects-suggestion', compact('projects')));
    }
}
