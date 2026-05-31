<?php

namespace Botble\Hotel\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Hotel\Facades\HotelHelper;
use Botble\Hotel\Http\Middleware\RedirectIfCustomer;
use Botble\Hotel\Http\Middleware\RedirectIfNotCustomer;
use Botble\Hotel\Models\Amenity;
use Botble\Hotel\Models\Booking;
use Botble\Hotel\Models\BookingAddress;
use Botble\Hotel\Models\BookingRoom;
use Botble\Hotel\Models\Currency;
use Botble\Hotel\Models\Customer;
use Botble\Hotel\Models\Feature;
use Botble\Hotel\Models\Food;
use Botble\Hotel\Models\FoodType;
use Botble\Hotel\Models\Place;
use Botble\Hotel\Models\Room;
use Botble\Hotel\Models\RoomCategory;
use Botble\Hotel\Models\RoomDate;
use Botble\Hotel\Models\Service;
use Botble\Hotel\Models\Tax;
use Botble\Hotel\Repositories\Eloquent\AmenityRepository;
use Botble\Hotel\Repositories\Eloquent\BookingAddressRepository;
use Botble\Hotel\Repositories\Eloquent\BookingRepository;
use Botble\Hotel\Repositories\Eloquent\BookingRoomRepository;
use Botble\Hotel\Repositories\Eloquent\CurrencyRepository;
use Botble\Hotel\Repositories\Eloquent\CustomerRepository;
use Botble\Hotel\Repositories\Eloquent\FeatureRepository;
use Botble\Hotel\Repositories\Eloquent\FoodRepository;
use Botble\Hotel\Repositories\Eloquent\FoodTypeRepository;
use Botble\Hotel\Repositories\Eloquent\PlaceRepository;
use Botble\Hotel\Repositories\Eloquent\RoomCategoryRepository;
use Botble\Hotel\Repositories\Eloquent\RoomDateRepository;
use Botble\Hotel\Repositories\Eloquent\RoomRepository;
use Botble\Hotel\Repositories\Eloquent\ServiceRepository;
use Botble\Hotel\Repositories\Eloquent\TaxRepository;
use Botble\Hotel\Repositories\Interfaces\AmenityInterface;
use Botble\Hotel\Repositories\Interfaces\BookingAddressInterface;
use Botble\Hotel\Repositories\Interfaces\BookingInterface;
use Botble\Hotel\Repositories\Interfaces\BookingRoomInterface;
use Botble\Hotel\Repositories\Interfaces\CurrencyInterface;
use Botble\Hotel\Repositories\Interfaces\CustomerInterface;
use Botble\Hotel\Repositories\Interfaces\FeatureInterface;
use Botble\Hotel\Repositories\Interfaces\FoodInterface;
use Botble\Hotel\Repositories\Interfaces\FoodTypeInterface;
use Botble\Hotel\Repositories\Interfaces\PlaceInterface;
use Botble\Hotel\Repositories\Interfaces\RoomCategoryInterface;
use Botble\Hotel\Repositories\Interfaces\RoomDateInterface;
use Botble\Hotel\Repositories\Interfaces\RoomInterface;
use Botble\Hotel\Repositories\Interfaces\ServiceInterface;
use Botble\Hotel\Repositories\Interfaces\TaxInterface;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Facades\SiteMapManager;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class HotelServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        config([
            'auth.guards.customer' => [
                'driver' => 'session',
                'provider' => 'customers',
            ],
            'auth.providers.customers' => [
                'driver' => 'eloquent',
                'model' => Customer::class,
            ],
            'auth.passwords.customers' => [
                'provider' => 'customers',
                'table' => 'ht_customer_password_resets',
                'expire' => 60,
            ],
        ]);
        /**
         * @var Router $router
         */
        $router = $this->app['router'];

        $router->aliasMiddleware('customer', RedirectIfNotCustomer::class);
        $router->aliasMiddleware('customer.guest', RedirectIfCustomer::class);

        $aliasLoader = AliasLoader::getInstance();

        if (! class_exists('HotelHelper')) {
            $aliasLoader->alias('HotelHelper', HotelHelper::class);
        }

        $this->app->bind(CurrencyInterface::class, function () {
            return new CurrencyRepository(new Currency());
        });

        $this->app->bind(RoomInterface::class, function () {
            return new RoomRepository(new Room());
        });

        $this->app->bind(RoomDateInterface::class, function () {
            return new RoomDateRepository(new RoomDate());
        });

        $this->app->bind(AmenityInterface::class, function () {
            return new AmenityRepository(new Amenity());
        });

        $this->app->bind(FoodInterface::class, function () {
            return new FoodRepository(new Food());
        });

        $this->app->bind(FoodTypeInterface::class, function () {
            return new FoodTypeRepository(new FoodType());
        });

        $this->app->bind(BookingInterface::class, function () {
            return new BookingRepository(new Booking());
        });

        $this->app->bind(BookingAddressInterface::class, function () {
            return new BookingAddressRepository(new BookingAddress());
        });

        $this->app->bind(BookingRoomInterface::class, function () {
            return new BookingRoomRepository(new BookingRoom());
        });

        $this->app->bind(CustomerInterface::class, function () {
            return new CustomerRepository(new Customer());
        });

        $this->app->bind(RoomCategoryInterface::class, function () {
            return new RoomCategoryRepository(new RoomCategory());
        });

        $this->app->bind(FeatureInterface::class, function () {
            return new FeatureRepository(new Feature());
        });

        $this->app->bind(ServiceInterface::class, function () {
            return new ServiceRepository(new Service());
        });

        $this->app->bind(PlaceInterface::class, function () {
            return new PlaceRepository(new Place());
        });

        $this->app->bind(TaxInterface::class, function () {
            return new TaxRepository(new Tax());
        });
    }

    public function boot(): void
    {
        SlugHelper::registerModule(Room::class, 'Rooms');
        SlugHelper::setPrefix(Room::class, 'rooms');

        SlugHelper::registerModule(Place::class, 'Places');
        SlugHelper::setPrefix(Place::class, 'places');

        SlugHelper::registerModule(Service::class, 'Services');
        SlugHelper::setPrefix(Service::class, 'services');

        ResetPassword::createUrlUsing(function (Customer $user, string $token) {
            return route('customer.password.reset.update', $token);
        });

        $this->setNamespace('plugins/hotel')
            ->loadAndPublishConfigurations(['permissions', 'hotel', 'email'])
            ->loadMigrations()
            ->loadHelpers()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes()
            ->publishAssets();

        $this->app['events']->listen(RouteMatched::class, function () {
            DashboardMenu::make()
                ->registerItem([
                    'id' => 'cms-plugins-hotel',
                    'priority' => 5,
                    'parent_id' => null,
                    'name' => 'plugins/hotel::hotel.name',
                    'icon' => 'fas fa-hotel',
                    'url' => route('room.index'),
                    'permissions' => ['room.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-room',
                    'priority' => 1,
                    'parent_id' => 'cms-plugins-hotel',
                    'name' => 'plugins/hotel::room.name',
                    'icon' => null,
                    'url' => route('room.index'),
                    'permissions' => ['room.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-room-category',
                    'priority' => 2,
                    'parent_id' => 'cms-plugins-hotel',
                    'name' => 'plugins/hotel::room-category.name',
                    'icon' => null,
                    'url' => route('room-category.index'),
                    'permissions' => ['room-category.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-amenities',
                    'priority' => 3,
                    'parent_id' => 'cms-plugins-hotel',
                    'name' => 'plugins/hotel::amenity.name',
                    'icon' => null,
                    'url' => route('amenity.index'),
                    'permissions' => ['amenity.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-invoice',
                    'priority' => 10,
                    'parent_id' => 'cms-plugins-booking',
                    'name' => 'plugins/hotel::invoice.name',
                    'url' => route('invoices.index'),
                    'permissions' => ['invoice.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-invoice-template',
                    'priority' => 11,
                    'parent_id' => 'cms-plugins-booking',
                    'name' => 'plugins/hotel::invoice.template.name',
                    'url' => route('invoice-template.index'),
                    'permissions' => ['invoice.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-hotel-invoice-settings',
                    'priority' => 999,
                    'parent_id' => 'cms-plugins-booking',
                    'name' => 'plugins/hotel::invoice.template.setting',
                    'icon' => null,
                    'url' => route('hotel.invoice.settings'),
                    'permissions' => ['hotel.settings'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-hotel-coupons',
                    'priority' => 14,
                    'parent_id' => null,
                    'name' => 'plugins/hotel::coupon.name',
                    'icon' => 'fas fa-percent',
                    'url' => route('coupons.index'),
                    'permissions' => ['coupons.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-food',
                    'priority' => 4,
                    'parent_id' => 'cms-plugins-hotel',
                    'name' => 'plugins/hotel::food.name',
                    'icon' => null,
                    'url' => route('food.index'),
                    'permissions' => ['food.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-food-type',
                    'priority' => 5,
                    'parent_id' => 'cms-plugins-hotel',
                    'name' => 'plugins/hotel::food-type.name',
                    'icon' => null,
                    'url' => route('food-type.index'),
                    'permissions' => ['food-type.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-feature',
                    'priority' => 6,
                    'parent_id' => 'cms-plugins-hotel',
                    'name' => 'plugins/hotel::feature.menu',
                    'icon' => null,
                    'url' => route('feature.index'),
                    'permissions' => ['feature.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-service',
                    'priority' => 6,
                    'parent_id' => 'cms-plugins-hotel',
                    'name' => 'plugins/hotel::service.name',
                    'icon' => null,
                    'url' => route('service.index'),
                    'permissions' => ['service.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-place',
                    'priority' => 6,
                    'parent_id' => 'cms-plugins-hotel',
                    'name' => 'plugins/hotel::place.name',
                    'icon' => null,
                    'url' => route('place.index'),
                    'permissions' => ['place.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-booking',
                    'priority' => 5,
                    'parent_id' => null,
                    'name' => 'plugins/hotel::booking.name',
                    'icon' => 'far fa-calendar-alt',
                    'url' => route('booking.index'),
                    'permissions' => ['booking.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-booking-reports',
                    'priority' => 1,
                    'parent_id' => 'cms-plugins-booking',
                    'name' => 'plugins/hotel::booking.reports',
                    'icon' => null,
                    'url' => route('booking.reports.index'),
                    'permissions' => ['booking.reports.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-booking-list',
                    'priority' => 2,
                    'parent_id' => 'cms-plugins-booking',
                    'name' => 'plugins/hotel::booking.name',
                    'icon' => null,
                    'url' => route('booking.index'),
                    'permissions' => ['booking.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-customer',
                    'priority' => 7,
                    'parent_id' => 'cms-plugins-hotel',
                    'name' => 'plugins/hotel::customer.name',
                    'icon' => null,
                    'url' => route('customer.index'),
                    'permissions' => ['customer.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-tax',
                    'priority' => 8,
                    'parent_id' => 'cms-plugins-hotel',
                    'name' => 'plugins/hotel::tax.name',
                    'icon' => null,
                    'url' => route('tax.index'),
                    'permissions' => ['tax.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-hotel-settings',
                    'priority' => 999,
                    'parent_id' => 'cms-plugins-hotel',
                    'name' => 'plugins/hotel::hotel.settings',
                    'icon' => null,
                    'url' => route('hotel.settings'),
                    'permissions' => ['hotel.settings'],
                ]);

            if (HotelHelper::isReviewEnabled()) {
                DashboardMenu::make()
                    ->registerItem([
                        'id' => 'cms-plugins-hotel-review',
                        'priority' => 5,
                        'parent_id' => 'cms-plugins-hotel',
                        'name' => 'plugins/hotel::review.name',
                        'icon' => null,
                        'url' => route('review.index'),
                        'permissions' => ['review.index'],
                    ]);
            }

            EmailHandler::addTemplateSettings(HOTEL_MODULE_SCREEN_NAME, config('plugins.hotel.email'));
        });

        if (defined('LANGUAGE_MODULE_SCREEN_NAME') && defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME')) {
            LanguageAdvancedManager::registerModule(Room::class, [
                'name',
                'description',
                'content',
            ]);

            LanguageAdvancedManager::registerModule(RoomCategory::class, [
                'name',
                'description',
            ]);

            LanguageAdvancedManager::registerModule(Amenity::class, [
                'name',
                'description',
            ]);

            LanguageAdvancedManager::registerModule(Food::class, [
                'name',
                'description',
            ]);

            LanguageAdvancedManager::registerModule(FoodType::class, [
                'name',
            ]);

            LanguageAdvancedManager::registerModule(Feature::class, [
                'name',
                'description',
            ]);

            LanguageAdvancedManager::registerModule(Service::class, [
                'name',
                'description',
            ]);

            LanguageAdvancedManager::registerModule(Place::class, [
                'name',
                'distance',
                'description',
                'content',
            ]);
        }

        SiteMapManager::registerKey(['rooms']);

        $this->app->register(EventServiceProvider::class);

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });
    }
}
