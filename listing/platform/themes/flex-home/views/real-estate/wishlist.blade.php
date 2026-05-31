<section class="main-homes wishlist-page">
    <div
        class="bgheadproject hidden-xs"
        style="background: url('{{ theme_option('breadcrumb_background') ? RvMedia::url(theme_option('breadcrumb_background')) : Theme::asset()->url('images/banner-du-an.jpg') }}')"
    >
        <div class="description">
            <div class="container-fluid w90">
                <h1 class="text-center">{{ SeoHelper::getTitle() }}</h1>
                {!! Theme::partial('breadcrumb') !!}
            </div>
        </div>
    </div>
    <div class="container-fluid w90 padtop30">
        @if($properties->isNotEmpty() || $projects->isNotEmpty())
            @if($properties->isNotEmpty())
                <div>
                    <h2>{{ __('Your Favorite Properties') }}</h2>
                    <div class="projecthome">
                        <div class="row rowm10">
                            @foreach ($properties as $property)
                                <div class="col-sm-6 col-lg-4 col-xl-3 colm10">
                                    {!! Theme::partial('real-estate.properties.item', ['property' => $property]) !!}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if ($properties->isNotEmpty())
                        <div class="col-sm-12">
                            <nav
                                class="d-flex justify-content-center pt-3"
                                aria-label="Page navigation example"
                            >
                                {!! $properties->withQueryString()->links() !!}
                            </nav>
                        </div>
                    @endif
                </div>
            @endif

            @if($projects->isNotEmpty())
                <div class="mt-20">
                    <h2>{{ __('Your Favorite Projects') }}</h2>
                    <div class="projecthome">
                        <div class="row rowm10">
                            @foreach ($projects as $project)
                                <div class="col-sm-6 col-lg-4 col-xl-3 colm10">
                                    {!! Theme::partial('real-estate.projects.item', ['project' => $project]) !!}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if ($projects->isNotEmpty())
                        <div class="col-sm-12">
                            <nav
                                class="d-flex justify-content-center pt-3"
                                aria-label="Page navigation example"
                            >
                                {!! $projects->withQueryString()->links() !!}
                            </nav>
                        </div>
                    @endif
                </div>
            @endif
        @else
            <div class="alert alert-warning">
                {{ __('No wishlist items found.') }}
            </div>
        @endif
    </div>
</section>
