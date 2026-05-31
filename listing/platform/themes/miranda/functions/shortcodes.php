<?php

use Botble\Gallery\Models\Gallery;
use Botble\Hotel\Models\Feature;
use Botble\Hotel\Models\Food;
use Botble\Hotel\Models\FoodType;
use Botble\Hotel\Models\Place;
use Botble\Hotel\Models\Room;
use Botble\Hotel\Models\RoomCategory;
use Botble\Hotel\Repositories\Interfaces\RoomInterface;
use Botble\Shortcode\Compilers\Shortcode;
use Botble\Testimonial\Models\Testimonial;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Supports\ThemeSupport;
use Botble\Theme\Supports\Youtube;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Request;

app()->booted(function () {
    ThemeSupport::registerGoogleMapsShortcode();
    ThemeSupport::registerYoutubeShortcode();

    add_shortcode('youtube-video', __('Youtube video'), __('Add youtube video'), function (Shortcode $shortcode) {
        Theme::asset()->usePath()->add('magnific-popup-css', 'css/magnific-popup.css');
        Theme::asset()->container('footer')->usePath()->add('jquery.magnific-popup', 'js/jquery.magnific-popup.min.js');

        return Theme::partial('short-codes.video', [
            'url' => Youtube::getYoutubeWatchURL($shortcode->url),
            'background_image' => $shortcode->background_image,
        ]);
    });

    if (is_plugin_active('testimonial')) {
        add_shortcode('testimonial', __('Testimonial'), __('Testimonial'), function (Shortcode $shortcode) {
            $testimonials = Testimonial::query()
                ->wherePublished()
                ->get();

            return Theme::partial('short-codes.testimonial', [
                'title' => $shortcode->title,
                'description' => $shortcode->description,
                'subtitle' => $shortcode->subtitle,
                'testimonials' => $testimonials,
            ]);
        });

        shortcode()->setAdminConfig('testimonial', function (array $attributes) {
            return Theme::partial('short-codes.testimonial-admin-config', compact('attributes'));
        });
    }

    if (is_plugin_active('blog')) {
        add_shortcode('featured-news', __('Featured News'), __('Featured News'), function (Shortcode $shortcode) {
            $posts = get_featured_posts(6, ['author']);

            return Theme::partial('short-codes.featured-news', [
                'title' => $shortcode->title,
                'subtitle' => $shortcode->subtitle,
                'description' => $shortcode->description,
                'posts' => $posts,
            ]);
        });

        shortcode()->setAdminConfig('featured-news', function (array $attributes) {
            return Theme::partial('short-codes.featured-news-admin-config', compact('attributes'));
        });
    }

    add_shortcode('video-introduction', __('Video Introduction'), __('Video Introduction'), function (Shortcode $shortcode) {
        Theme::asset()->usePath()->add('magnific-popup-css', 'css/magnific-popup.css');
        Theme::asset()->container('footer')->usePath()->add('jquery.magnific-popup', 'js/jquery.magnific-popup.min.js');

        return Theme::partial('short-codes.video-introduction', [
            'title' => $shortcode->title,
            'sub_title' => $shortcode->sub_title,
            'subtitle' => $shortcode->subtitle,
            'description' => $shortcode->description,
            'content' => $shortcode->content,
            'background_image' => $shortcode->background_image,
            'video_image' => $shortcode->video_image,
            'video_url' => $shortcode->video_url,
            'button_text' => $shortcode->button_text,
            'button_url' => $shortcode->button_url,
        ]);
    });

    shortcode()->setAdminConfig('video-introduction', function (array $attributes) {
        return Theme::partial('short-codes.video-introduction-admin-config', compact('attributes'));
    });

    add_shortcode('rooms-introduction', __('Rooms Introduction'), __('Rooms Introduction'), function (Shortcode $shortcode) {
        return Theme::partial('short-codes.rooms-introduction', [
            'title' => $shortcode->title,
            'description' => $shortcode->description,
            'subtitle' => $shortcode->subtitle,
            'background_image' => $shortcode->background_image,
            'first_image' => $shortcode->first_image,
            'second_image' => $shortcode->second_image,
            'third_image' => $shortcode->third_image,
            'button_text' => $shortcode->button_text,
            'button_url' => $shortcode->button_url,
        ]);
    });

    shortcode()->setAdminConfig('rooms-introduction', function (array $attributes) {
        return Theme::partial('short-codes.rooms-introduction-admin-config', compact('attributes'));
    });

    add_shortcode('hotel-about', __('Hotel About'), __('Hotel About'), function (Shortcode $shortcode) {
        return Theme::partial('short-codes.hotel-about', [
            'title' => $shortcode->title,
            'description' => $shortcode->description,
            'subtitle' => $shortcode->subtitle,
            'block_icon_1' => $shortcode->block_icon_1,
            'block_text_1' => $shortcode->block_text_1,
            'block_link_1' => $shortcode->block_link_1,
            'block_icon_2' => $shortcode->block_icon_2,
            'block_text_2' => $shortcode->block_text_2,
            'block_link_2' => $shortcode->block_link_2,
            'block_icon_3' => $shortcode->block_icon_3,
            'block_text_3' => $shortcode->block_text_3,
            'block_link_3' => $shortcode->block_link_3,
            'block_icon_4' => $shortcode->block_icon_4,
            'block_text_4' => $shortcode->block_text_4,
            'block_link_4' => $shortcode->block_link_4,
            'block_icon_5' => $shortcode->block_icon_5,
            'block_text_5' => $shortcode->block_text_5,
            'block_link_5' => $shortcode->block_link_5,
        ]);
    });

    shortcode()->setAdminConfig('hotel-about', function (array $attributes) {
        return Theme::partial('short-codes.hotel-about-admin-config', compact('attributes'));
    });

    add_shortcode('contact-info', __('Contact information'), __('Contact information'), function () {
        return Theme::partial('short-codes.contact-info');
    });

    shortcode()->setAdminConfig('youtube-video', function (array $attributes) {
        return Theme::partial('short-codes.video-admin-config', compact('attributes'));
    });

    add_shortcode('home-banner', 'Home Banner', 'Home Banner', function (Shortcode $shortcode) {
        return Theme::partial('short-codes.home-banner', [
            'shortcode' => $shortcode,

        ]);
    });

    shortcode()->setAdminConfig('home-banner', function (array $attributes) {
        return Theme::partial('short-codes.home-banner-admin-config', compact('attributes'));
    });

    add_shortcode('rewrite-theme-option', 'Rewrite Theme Option', 'Rewrite Theme Option', function (Shortcode $shortcode) {
        return Theme::partial('short-codes.rewrite-theme-option', [
            'shortcode' => $shortcode,
        ]);
    });

    shortcode()->setAdminConfig('rewrite-theme-option', function (array $attributes) {
        return Theme::partial('short-codes.rewrite-theme-option-admin-config', compact('attributes'));
    });

    if (is_plugin_active('hotel')) {
        add_shortcode(
            'hotel-featured-features',
            __('Hotel Featured Features'),
            __('Hotel Featured Features'),
            function (Shortcode $shortcode) {
                $features = Feature::query()
                    ->wherePublished()
                    ->where('is_featured', true)
                    ->get();

                return Theme::partial('short-codes.hotel-featured-features', [
                    'title' => $shortcode->title,
                    'description' => $shortcode->description,
                    'subtitle' => $shortcode->subtitle,
                    'button_text' => $shortcode->button_text,
                    'button_url' => $shortcode->button_url,
                    'features' => $features,
                ]);
            }
        );

        shortcode()->setAdminConfig('hotel-featured-features', function (array $attributes) {
            return Theme::partial('short-codes.hotel-featured-features-admin-config', compact('attributes'));
        });

        add_shortcode('rooms', __('Rooms'), __('Rooms'), function (Shortcode $shortcode) {
            $rooms = Room::query()
                ->wherePublished()
                ->with(['slugable'])
                ->get();

            return Theme::partial('short-codes.rooms', compact('shortcode', 'rooms'));
        });

        shortcode()->setAdminConfig('rooms', function (array $attributes) {
            $rooms = Room::query()
                ->wherePublished()
                ->with(['slugable'])
                ->get();

            return Theme::partial('short-codes.rooms-admin-config', compact('attributes', 'rooms'));
        });

        add_shortcode('room-categories', __('Room Categories'), __('Room Categories'), function (Shortcode $shortcode) {
            $categories = RoomCategory::query()
                ->wherePublished()
                ->where('is_featured', true)
                ->with([
                    'rooms' => function ($query) {
                        $query
                            ->latest()
                            ->with(['slugable'])
                            ->wherePublished();
                    },
                    'rooms.slugable',
                ])
                ->orderBy('order')
                ->get();

            return Theme::partial('short-codes.room-categories', [
                'title' => $shortcode->title,
                'sub_title' => $shortcode->sub_title,
                'subtitle' => $shortcode->subtitle,
                'background_image' => $shortcode->background_image,
                'categories' => $categories,
            ]);
        });

        shortcode()->setAdminConfig('room-categories', function (array $attributes) {
            return Theme::partial('short-codes.room-categories-admin-config', compact('attributes'));
        });

        add_shortcode('food-types', __('Food Types'), __('Food Types'), function () {
            $foodTypes = FoodType::query()
                ->wherePublished()
                ->with(['foods'])
                ->get();

            return Theme::partial('short-codes.food-types', compact('foodTypes'));
        });

        add_shortcode('foods', __('Foods'), __('Foods'), function (Shortcode $shortcode) {
            $foods = Food::query()
                ->wherePublished()
                ->with(['type'])
                ->get();

            return Theme::partial('short-codes.foods', [
                'title' => $shortcode->title,
                'sub_title' => $shortcode->sub_title,
                'subtitle' => $shortcode->subtitle,
                'foods' => $foods,
            ]);
        });

        shortcode()->setAdminConfig('foods', function (array $attributes) {
            return Theme::partial('short-codes.foods-admin-config', compact('attributes'));
        });

        add_shortcode(
            'hotel-core-features',
            __('Hotel Core Features'),
            __('Hotel Core Features'),
            function (Shortcode $shortcode) {
                $features = Feature::query()
                    ->wherePublished()
                    ->get();

                return Theme::partial('short-codes.hotel-core-features', [
                    'title' => $shortcode->title,
                    'sub_title' => $shortcode->sub_title,
                    'subtitle' => $shortcode->subtitle,
                    'features' => $features,
                ]);
            }
        );

        shortcode()->setAdminConfig('hotel-featured-features', function (array $attributes) {
            return Theme::partial('short-codes.hotel-core-features-admin-config', compact('attributes'));
        });

        add_shortcode(
            'check-availability-form',
            __('Check Availability Form'),
            __('Check Availability Form'),
            function () {
                return Theme::partial('short-codes.check-availability-form');
            }
        );

        $dateFormat = 'Y-m-d';

        add_shortcode('our-offers', __('Our offers'), __('Our offers'), function () use ($dateFormat) {
            $condition = [
                'start_date' => Carbon::now()->format($dateFormat),
                'end_date' => Carbon::now()->addDay()->format($dateFormat),
                'adults' => 1,
            ];

            $allRooms = Room::query()
                ->wherePublished()
                ->where('is_featured', true)
                ->with([
                    'slugable',
                    'amenities',
                    'category',
                    'activeBookingRooms' => function ($query) use ($condition, $dateFormat) {
                        return $query
                            ->whereDate('start_date', '<=', date($dateFormat, strtotime($condition['start_date'])))
                            ->whereDate('end_date', '>=', date($dateFormat, strtotime($condition['end_date'])));
                    },
                    'activeRoomDates' => function ($query) use ($condition, $dateFormat) {
                        return $query
                            ->whereDate('start_date', '>=', date($dateFormat, strtotime($condition['start_date'])))
                            ->whereDate('end_date', '<=', date($dateFormat, strtotime($condition['end_date'])))
                            ->take(40);
                    },
                ])
                ->get();

            $nights = 1;

            $rooms = [];
            foreach ($allRooms as $allRoom) {
                if ($allRoom->isAvailableAt($condition)) {
                    $allRoom->total_price = $allRoom->getRoomTotalPrice(
                        $condition['start_date'],
                        $condition['end_date'],
                        $nights
                    );
                    $rooms[] = $allRoom;
                }
            }

            return Theme::partial('short-codes.our-offers', compact('rooms'));
        });

        add_shortcode('all-rooms', __('All Rooms'), __('Display all rooms'), function () use ($dateFormat) {
            try {
                if (Request::input('start_date') && Request::input('end_date')) {
                    $startDate = Carbon::createFromFormat('d-m-Y', Request::input('start_date'));
                    $endDate = Carbon::createFromFormat('d-m-Y', Request::input('end_date'));
                } else {
                    $startDate = Carbon::now();
                    $endDate = Carbon::now()->addDay();
                }
            } catch (Exception) {
                $startDate = Carbon::now();
                $endDate = Carbon::now()->addDay();
            }

            $filters = [
                'keyword' => Request::query('q'),
            ];

            $condition = [
                'start_date' => $startDate->format($dateFormat),
                'end_date' => $endDate->format($dateFormat),
                'adults' => Request::input('adults', 1),
            ];

            $params = [
                'paginate' => [
                    'per_page' => 100,
                    'current_paged' => (int)Request::input('page', 1),
                ],
                'with' => [
                    'amenities',
                    'amenities.metadata',
                    'slugable',
                    'activeBookingRooms' => function ($query) use ($startDate, $endDate) {
                        return $query
                            ->where(function ($query) use ($startDate, $endDate) {
                                return $query
                                    ->whereDate('start_date', '>=', $startDate)
                                    ->whereDate('start_date', '<=', $endDate);
                            })
                            ->orWhere(function ($query) use ($startDate, $endDate) {
                                return $query
                                    ->whereDate('end_date', '>=', $startDate)
                                    ->whereDate('end_date', '<=', $endDate);
                            })
                            ->orWhere(function ($query) use ($startDate, $endDate) {
                                return $query
                                    ->whereDate('start_date', '<=', $startDate)
                                    ->whereDate('end_date', '>=', $endDate);
                            })
                            ->orWhere(function ($query) use ($startDate, $endDate) {
                                return $query
                                    ->whereDate('start_date', '>=', $startDate)
                                    ->whereDate('end_date', '<=', $endDate);
                            });
                    },
                    'activeRoomDates' => function ($query) use ($startDate, $endDate) {
                        return $query
                            ->whereDate('start_date', '>=', $startDate)
                            ->whereDate('end_date', '<=', $endDate)
                            ->take(40);
                    },
                ],
            ];

            $queriedRooms = app(RoomInterface::class)->getRooms($filters, $params);

            $nights = $endDate->diffInDays($startDate);

            $rooms = [];

            foreach ($queriedRooms as &$room) {
                if ($room->isAvailableAt($condition)) {
                    $room->total_price = $room->getRoomTotalPrice($condition['start_date'], $condition['end_date'], $nights);

                    $rooms[] = $room;
                }
            }

            $rooms = new LengthAwarePaginator($rooms, count($rooms), 100, Paginator::resolveCurrentPage(), ['path' => Paginator::resolveCurrentPath()]);

            return Theme::partial('short-codes.all-rooms', compact('rooms', 'nights'));
        });
    }

    if (is_plugin_active('gallery')) {
        add_shortcode('all-galleries', __('All Galleries'), __('All Galleries'), function (Shortcode $shortcode) {
            $galleries = Gallery::query()
                ->wherePublished()
                ->with(['slugable'])
                ->get();

            return Theme::partial('short-codes.all-galleries', [
                'title' => $shortcode->title,
                'sub_title' => $shortcode->sub_title,
                'subtitle' => $shortcode->subtitle,
                'galleries' => $galleries,
            ]);
        });

        shortcode()->setAdminConfig('all-galleries', function (array $attributes) {
            return Theme::partial('short-codes.all-galleries-admin-config', compact('attributes'));
        });

        add_shortcode('places', __('Places'), __('Places'), function () {
            $places = Place::query()
                ->wherePublished()
                ->get();

            return Theme::partial('short-codes.places', [
                'places' => $places,
            ]);
        });
    }

    if (is_plugin_active('contact')) {
        add_filter(CONTACT_FORM_TEMPLATE_VIEW, function () {
            return Theme::getThemeNamespace() . '::partials.short-codes.contact-form';
        }, 120);

        add_shortcode('contact-home', __('Contact Home Page'), __('Contact Home Page'), function (Shortcode $shortcode) {
            return Theme::partial('short-codes.contact-form-homepage', compact('shortcode'));
        });

        shortcode()->setAdminConfig('contact-home', function (array $attributes) {
            return Theme::partial('short-codes.contact-form-homepage-admin-config', compact('attributes'));
        });
    }

    if (is_plugin_active('newsletter')) {
        add_shortcode('newsletter', __('Newsletter'), __('Newsletter'), function (Shortcode $shortcode) {
            return Theme::partial('short-codes.newsletter', compact('shortcode'));
        });

        shortcode()->setAdminConfig('newsletter', function (array $attributes) {
            return Theme::partial('short-codes.newsletter-admin-config', compact('attributes'));
        });
    }
});
