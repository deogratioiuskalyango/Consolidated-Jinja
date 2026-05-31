<?php

namespace Botble\Hotel\Supports;

use Botble\Base\Facades\BaseHelper;
use Botble\Hotel\Enums\ReviewStatusEnum;
use Botble\Theme\Facades\Theme;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Throwable;

class HotelSupport
{
    public function isEnableEmailVerification(): bool
    {
        return (bool)$this->getSetting('verify_customer_email', 0);
    }

    public function getSettingPrefix(): string|null
    {
        return config('plugins.hotel.general.prefix');
    }

    public function isReviewEnabled(): bool
    {
        return (bool)setting('hotel_enable_review_room', 1);
    }

    public function getReviewExtraData(): array
    {
        if (! $this->isReviewEnabled()) {
            return [];
        }

        return [
            'withCount' => [
                'reviews' => function ($query) {
                    $query->where('status', ReviewStatusEnum::APPROVED);
                },
            ],
            'withAvg' => ['reviews', 'star'],
        ];
    }

    public function getSetting(string $key, bool|int|string|null $default = ''): array|int|string|null
    {
        return setting($this->getSettingPrefix() . $key, $default);
    }

    public function loadCountriesStatesCitiesFromPluginLocation(): bool
    {
        if (! is_plugin_active('location')) {
            return false;
        }

        return (bool)$this->getSetting('load_countries_states_cities_from_location_plugin', 0);
    }

    public function viewPath(string $view): string
    {
        $themeView = Theme::getThemeNamespace() . '::views.hotel.' . $view;

        if (view()->exists($themeView)) {
            return $themeView;
        }

        return 'plugins/hotel::themes.' . $view;
    }

    public function getRoomFilters(Request|array $request): array
    {
        if ($request instanceof Request) {
            $request = $request->input();
        }

        $data = [
            'keyword' => BaseHelper::clean(Arr::get($request, 'q')),
            'start_date' => Arr::get($request, 'start_date'),
            'end_date' => Arr::get($request, 'end_date'),
            'adults' => Arr::get($request, 'adults', 1),
            'page' => Arr::get($request, 'page', 1),
            'per_page' => Arr::get($request, 'per_page', 10),
        ];

        $validator = Validator::make($data, [
            'keyword' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'adults' => ['nullable', 'int', 'min:1'],
            'page' => ['nullable', 'int', 'min:1'],
            'per_page' => ['nullable', 'int', 'min:1'],
        ]);

        return $validator->valid();
    }

    public function getRoomBookingParams(): array
    {
        $request = request();

        try {
            if ($request->input('start_date') && $request->input('end_date')) {
                $startDate = Carbon::createFromFormat('d-m-Y', $request->input('start_date'));
                $endDate = Carbon::createFromFormat('d-m-Y', $request->input('end_date'));
            } else {
                $startDate = Carbon::now();
                $endDate = Carbon::now()->addDay();
            }
        } catch (Throwable) {
            $startDate = Carbon::now();
            $endDate = Carbon::now()->addDay();
        }

        $adults = $request->input('adults', 1);

        $nights = $endDate->diffInDays($startDate);

        return [
            $startDate,
            $endDate,
            $adults,
            $nights,
        ];
    }
}
