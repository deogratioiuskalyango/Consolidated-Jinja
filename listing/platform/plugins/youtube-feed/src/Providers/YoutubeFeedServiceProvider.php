<?php

namespace Botble\YoutubeFeed\Providers;

use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Base\Facades\DashboardMenu;
use Illuminate\Support\ServiceProvider;

class YoutubeFeedServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/youtube-feed')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishViews();

        DashboardMenu::beforeRetrieving(function (): void {
            DashboardMenu::make()
                ->registerItem([
                    'id' => 'cms-plugins-youtube-feed',
                    'priority' => 418,
                    'name' => 'plugins/youtube-feed::youtube-feed.name',
                    'icon' => 'ti ti-brand-youtube',
                    'url' => route('youtube-feed.settings'),
                    'permissions' => ['youtube-feed.settings'],
                ]);
        });

        $this->app->booted(function (): void {
            if (! function_exists('add_shortcode')) {
                return;
            }

            add_shortcode(
                'youtube-feed',
                trans('plugins/youtube-feed::youtube-feed.shortcode.name'),
                trans('plugins/youtube-feed::youtube-feed.shortcode.description'),
                function ($compiled): string {
                    $title = trim((string) $compiled->get('title', ''));
                    $subtitle = trim((string) $compiled->get('subtitle', ''));

                    return view('plugins/youtube-feed::shortcodes.youtube-feed', [
                        'title' => $title !== '' ? $title : null,
                        'subtitle' => $subtitle !== '' ? $subtitle : null,
                    ])->render();
                }
            );
        });
    }
}
