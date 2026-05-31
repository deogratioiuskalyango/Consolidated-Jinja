<?php

namespace Database\Seeders;

use Botble\ACL\Models\User;
use Botble\Base\Supports\BaseSeeder;
use Botble\Location\Models\State;
use Botble\RealEstate\Enums\ProjectStatusEnum;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\Facility;
use Botble\RealEstate\Models\Feature;
use Botble\RealEstate\Models\Investor;
use Botble\RealEstate\Models\Project;
use Botble\Slug\Facades\SlugHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectSeeder extends BaseSeeder
{
    public function run(): void
    {
        $this->uploadFiles('properties');

        Project::query()->truncate();

        DB::table('re_project_categories')->truncate();
        DB::table('re_project_features')->truncate();
        DB::table('re_facilities_distances')->where('reference_type', Project::class)->delete();

        $users = User::query()->pluck('id');
        $categories = Category::query()->pluck('id');
        $investors = Investor::query()->pluck('id');
        $features = Feature::query()->pluck('id');
        $featuresCount = $features->count();
        $facilitiesCount = Facility::query()->count();
        $states = State::query()->with(['country', 'cities'])->limit(6)->oldest()->get();

        $projects = [
            'Walnut Park Apartments',
            'Sunshine Wonder Villas',
            'Diamond Island',
            'The Nassim',
            'Vinhomes Grand Park',
            'The Metropole Thu Thiem',
            'Villa on Grand Avenue',
            'Traditional Food Restaurant',
            'Villa on Hollywood Boulevard',
            'Office Space at Northwest 107th',
            'Home in Merrick Way',
            'Adarsh Greens',
            'Rustomjee Evershine Global City',
            'Godrej Exquisite',
            'Godrej Prime',
            'PS Panache',
            'Upturn Atmiya Centria',
            'Brigade Oasis',
        ];

        $descriptions = [
            'A premier residential development offering modern living with exceptional amenities and thoughtful design in a prime location.',
            'Luxury living redefined with world-class facilities, stunning architecture, and convenient access to urban conveniences.',
            'An innovative mixed-use development combining residential comfort with commercial convenience in a vibrant community setting.',
            'Experience elevated living in this thoughtfully planned community featuring green spaces, modern homes, and premium amenities.',
            'A landmark development setting new standards for quality construction, sustainable design, and community living.',
            'Premium residences designed for discerning homeowners who appreciate quality, location, and lifestyle excellence.',
            'A masterfully planned community offering diverse housing options with shared amenities and beautiful surroundings.',
            'Modern urban living at its finest with sleek design, smart features, and a commitment to sustainable development.',
        ];

        $contents = [
            'Welcome to our flagship development project, a masterfully planned community that sets new standards for modern living. This exceptional project features a diverse range of residential options, from cozy apartments to spacious family homes, all designed with attention to detail and quality construction. Residents enjoy access to world-class amenities including a state-of-the-art fitness center, swimming pools, landscaped gardens, and community gathering spaces. The development incorporates sustainable building practices and energy-efficient systems, reflecting our commitment to environmental responsibility. Located in a prime area with excellent connectivity to schools, shopping centers, healthcare facilities, and public transportation, this project offers the perfect balance of convenience and tranquility. Our experienced development team has created a community where families can thrive, professionals can unwind, and investors can expect strong returns. With flexible payment plans and competitive pricing, this is an opportunity not to be missed.',
            'This prestigious development represents the pinnacle of urban living, combining architectural excellence with practical functionality. Every aspect of this project has been carefully considered, from the grand entrance lobbies to the thoughtfully designed living spaces. The project features multiple building configurations to suit different lifestyles and preferences, all unified by consistent quality and aesthetic appeal. Residents benefit from comprehensive amenities including covered parking, 24/7 security, recreational facilities, and beautifully maintained common areas. The development is strategically located to offer easy access to major business districts, educational institutions, and entertainment venues while providing a peaceful retreat from city bustle. Our commitment to quality extends beyond construction to include attentive property management and responsive customer service. Whether you are seeking a primary residence or an investment property, this development offers exceptional value and lasting appeal.',
            'Discover a new standard of community living in this thoughtfully designed development project. Spanning multiple phases, this ambitious project creates a self-contained neighborhood with all the conveniences of modern life. The architectural design draws inspiration from contemporary trends while incorporating timeless elements that ensure lasting appeal. Residents enjoy exclusive access to premium amenities including clubhouse facilities, sports courts, children play areas, and jogging trails. The development places strong emphasis on green spaces and environmental sustainability, with extensive landscaping and eco-friendly building systems. The location offers strategic advantages including proximity to employment centers, quality schools, healthcare facilities, and shopping destinations. Our development team brings decades of experience in creating successful residential communities, ensuring that every detail meets the highest standards. This is more than a place to live – it is a community designed for life.',
        ];

        $locations = [
            '100 Innovation Drive, Tech District',
            '250 Parkview Boulevard, Central Heights',
            '75 Harbor Gateway, Waterfront Quarter',
            '400 Skyline Avenue, Downtown Core',
            '180 Garden Terrace, Green Valley',
            '320 Summit Road, Highland Park',
            '50 Riverside Way, Marina District',
            '600 Metropolitan Center, Business Hub',
        ];

        foreach ($projects as $project) {
            $images = [];
            $randomImages = array_rand(array_flip(range(1, 12)), rand(5, 12));

            foreach ((array) $randomImages as $image) {
                $images[] = $this->filePath("properties/$image.jpg");
            }

            $price = rand(100, 10000);

            $state = $states->random();

            $dateFinish = Carbon::now()->addMonths(rand(6, 36));
            $dateSell = Carbon::now()->subMonths(rand(1, 12));

            /**
             * @var Project $project
             */
            $project = Project::query()->create([
                'unique_id' => strtoupper(Str::random(6)),
                'name' => $project,
                'description' => $descriptions[array_rand($descriptions)],
                'content' => $contents[array_rand($contents)],
                'images' => $images,
                'location' => $locations[array_rand($locations)],
                'investor_id' => $investors->random(),
                'number_block' => rand(1, 10),
                'number_floor' => rand(1, 50),
                'number_flat' => rand(10, 5000),
                'is_featured' => rand(0, 1),
                'date_finish' => $dateFinish,
                'date_sell' => $dateSell,
                'latitude' => 42.4772 + (rand(0, 15000) / 10000),
                'longitude' => -76.7517 + (rand(0, 20000) / 10000),
                'country_id' => $state->country->id,
                'state_id' => $state->id,
                'city_id' => $state->cities->isNotEmpty() ? $state->cities->random()->id : null,
                'status' => ProjectStatusEnum::SELLING,
                'price_from' => $price,
                'price_to' => $price + rand(500, 10000),
                'views' => rand(100, 10000),
                'currency_id' => 1,
                'author_id' => $users->random(),
                'author_type' => User::class,
            ]);

            $project->categories()->attach($categories->random(rand(1, 3)));
            $project->features()->attach($features->random(rand($featuresCount - 8, $featuresCount)));

            foreach (range(1, $facilitiesCount) as $facilityId) {
                $distance = sprintf('%skm', rand(1, 20));
                $project->facilities()->attach($facilityId, ['distance' => $distance]);
            }

            SlugHelper::createSlug($project);
        }
    }
}
