<?php

namespace Database\Seeders;

use Botble\Base\Facades\MetaBox;
use Botble\Base\Supports\BaseSeeder;
use Botble\Location\Models\State;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\Facility;
use Botble\RealEstate\Models\Feature;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Botble\Slug\Facades\SlugHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertySeeder extends BaseSeeder
{
    public function run(): void
    {
        Property::query()->truncate();
        DB::table('re_property_features')->truncate();
        DB::table('re_property_categories')->truncate();
        DB::table('re_facilities_distances')->where('reference_type', Property::class)->delete();
        DB::table('slugs')->where('reference_type', Property::class)->delete();

        $properties = [
            '3 Beds Villa Calpe, Alicante',
            'Lavida Plus Office-tel 1 Bedroom',
            'Vinhomes Grand Park Studio 1 Bedroom',
            'The Sun Avenue Office-tel 1 Bedroom',
            'Property For sale, Johannesburg, South Africa',
            'Stunning French Inspired Manor',
            'Villa for sale at Bermuda Dunes',
            'Walnut Park Apartment',
            '5 beds luxury house',
            'Family Victorian "View" Home',
            'Osaka Heights Apartment',
            'Private Estate Magnificent Views',
            'Thompson Road House for rent',
            'Brand New 1 Bedroom Apartment In First Class Location',
            'Elegant family home presents premium modern living',
            'Luxury Apartments in Singapore for Sale',
            '5 room luxury penthouse for sale in Kuala Lumpur',
            '2 Floor house in Compound Pejaten Barat Kemang',
            'Apartment Muiderstraatweg in Diemen',
            'Nice Apartment for rent in Berlin',
            'Pumpkin Key - Private Island',
            'Maplewood Estates',
            'Pine Ridge Manor',
            'Oak Hill Residences',
            'Sunnybrook Villas',
            'Riverstone Condominiums',
            'Cedar Park Apartments',
            'Lakeside Retreat',
            'Willow Creek Homes',
            'Grandview Heights',
            'Forest Glen Cottages',
            'Harborview Towers',
            'Meadowlands Estates',
            'Highland Meadows',
            'Brookfield Gardens',
            'Silverwood Villas',
            'Evergreen Terrace',
            'Golden Gate Residences',
            'Spring Blossom Park',
            'Horizon Pointe',
            'Whispering Pines Lodge',
            'Sunset Ridge',
            'Timberline Estates',
            'Crystal Lake Condos',
            'Briarwood Apartments',
            'Summit View',
            'Elmwood Park',
            'Stonegate Homes',
            'Rosewood Villas',
            'Prairie Meadows',
            'Hawthorne Heights',
            'Sierra Vista',
            'Autumn Leaves',
            'Blue Sky Residences',
            'Pebble Creek',
            'Magnolia Manor',
            'Cherry Blossom Estates',
            'Windsor Park',
            'Seaside Villas',
            'Mountain View Retreat',
            'Amberwood Apartments',
        ];

        $floorPlans = collect(
            [
                [
                    'name' => 'First Floor',
                    'bedrooms' => 3,
                    'bathrooms' => 2,
                    'image' => $this->filePath('properties/floor.png'),
                ],
                [
                    'name' => 'Second Floor',
                    'bedrooms' => 2,
                    'bathrooms' => 1,
                    'image' => $this->filePath('properties/floor.png'),
                ],
            ]
        )
            ->map(function ($floorPlan) {
                return collect($floorPlan)->map(function ($value, $key) {
                    return [
                        'key' => $key,
                        'value' => (string) $value,
                    ];
                })->toArray();
            })
            ->toArray();

        $projects = Project::query()->pluck('id');
        $states = State::query()->with(['country', 'cities'])->limit(6)->oldest()->get();
        $accounts = Account::query()->pluck('id');
        $categories = Category::query()->pluck('id');
        $features = Feature::query()->pluck('id');
        $featuresCount = $features->count();
        $facilitiesCount = Facility::query()->count();

        $descriptions = [
            'Beautiful property featuring modern design and premium finishes throughout. This stunning home offers an open floor plan perfect for entertaining.',
            'Exceptional residence in a prime location with easy access to schools, shopping, and public transportation. Recently renovated with high-end fixtures.',
            'Charming property with spacious rooms and abundant natural light. The well-maintained garden adds to the appeal of this lovely home.',
            'Contemporary living at its finest. This property boasts state-of-the-art amenities and a sleek, modern aesthetic throughout.',
            'Elegant home with timeless architecture and thoughtful design elements. Perfect for families seeking comfort and style.',
            'Stunning property offering panoramic views and luxurious finishes. Every detail has been carefully considered in this exceptional home.',
            'Spacious and bright residence with an excellent layout for modern living. Move-in ready with all appliances included.',
            'Prime real estate opportunity in a desirable neighborhood. This property combines location, quality, and value perfectly.',
            'Meticulously maintained property with upgrades throughout. Features include hardwood floors, granite countertops, and stainless appliances.',
            'Inviting home with a warm atmosphere and practical layout. The outdoor space is perfect for relaxation and entertainment.',
        ];

        $contents = [
            'Welcome to this exceptional property that redefines modern living. From the moment you enter, you will be captivated by the attention to detail and quality craftsmanship evident throughout. The open-concept living area flows seamlessly into the gourmet kitchen, featuring premium appliances, quartz countertops, and custom cabinetry. Large windows flood the space with natural light while offering views of the beautifully landscaped surroundings. The primary suite is a true retreat, complete with a spa-like bathroom and generous walk-in closet. Additional bedrooms are well-appointed, perfect for family members or guests. The outdoor living space extends your entertaining options with a covered patio and mature landscaping. Located in a sought-after neighborhood with excellent schools, convenient shopping, and easy highway access, this property offers the perfect combination of comfort, style, and location.',
            'This stunning residence offers an unparalleled living experience in one of the most desirable locations. The thoughtfully designed floor plan maximizes space and functionality while maintaining an elegant aesthetic. Upon entering, you are greeted by soaring ceilings and an abundance of natural light that highlights the premium finishes throughout. The chef-inspired kitchen features top-of-the-line appliances, a large center island, and ample storage. The living areas are perfect for both intimate gatherings and large-scale entertaining. Each bedroom is generously sized with excellent closet space. The primary suite includes a luxurious bathroom with dual vanities, a soaking tub, and a separate shower. Outside, the property boasts professional landscaping, a private backyard, and a covered outdoor entertaining area. Smart home features, energy-efficient systems, and a two-car garage complete this exceptional offering.',
            'Discover your dream home in this beautifully appointed property that combines classic elegance with modern convenience. The grand entryway sets the tone for the sophisticated living spaces that follow. Gleaming hardwood floors flow throughout the main level, connecting the formal living room, dining area, and family room. The updated kitchen is a chef delight with granite counters, stainless steel appliances, and a breakfast nook overlooking the garden. Upstairs, the spacious primary suite features a sitting area, walk-in closet, and renovated bathroom. Additional bedrooms provide flexibility for family, guests, or a home office. The finished lower level offers extra living space for recreation or entertainment. Outside, mature trees provide privacy while the manicured lawn and garden beds enhance curb appeal. This is a rare opportunity to own a property that offers both character and modern updates.',
        ];

        $locations = [
            '123 Oak Street, Riverside Heights',
            '456 Maple Avenue, Downtown District',
            '789 Pine Road, Garden Quarter',
            '321 Cedar Lane, Lakeside Park',
            '654 Birch Boulevard, Sunset Hills',
            '987 Elm Drive, Mountain View',
            '147 Willow Way, Harbor Point',
            '258 Spruce Court, Valley Green',
            '369 Ash Circle, Meadow Springs',
            '741 Hickory Place, Forest Glen',
        ];

        foreach ($properties as $property) {
            $type = rand(0, 1) ? 'sale' : 'rent';

            $images = [];
            $randomImages = array_rand(array_flip(range(1, 12)), rand(5, 12));

            foreach ((array) $randomImages as $image) {
                $images[] = $this->filePath("properties/$image.jpg");
            }

            $state = $states->random();

            /**
             * @var Property $property
             */
            $property = Property::query()->forceCreate([
                'unique_id' => strtoupper(Str::random(6)),
                'name' => $property,
                'description' => $descriptions[array_rand($descriptions)],
                'content' => $contents[array_rand($contents)],
                'location' => $locations[array_rand($locations)],
                'images' => $images,
                'project_id' => $projects->isNotEmpty() ? $projects->random() : null,
                'author_id' => $accounts->random(),
                'author_type' => Account::class,
                'number_bedroom' => rand(1, 10),
                'number_bathroom' => rand(1, 10),
                'number_floor' => rand(1, 100),
                'square' => rand(1, 100) * 10,
                'price' => rand(100, 10000) * 100,
                'is_featured' => (bool) rand(0, 1),
                'status' => $type === 'sale' ? 'selling' : 'renting',
                'type' => $type,
                'moderation_status' => ModerationStatusEnum::APPROVED,
                'expire_date' => Carbon::now()->days(rand(30, 365)),
                'never_expired' => true,
                'latitude' => 42.4772 + (rand(0, 15000) / 10000),
                'longitude' => -76.7517 + (rand(0, 20000) / 10000),
                'views' => rand(0, 100000),
                'country_id' => $state->country->id,
                'state_id' => $state->id,
                'city_id' => $state->cities->isNotEmpty() ? $state->cities->random()->id : null,
                'floor_plans' => $floorPlans,
            ]);

            MetaBox::saveMetaBoxData($property, 'video_url', 'https://youtu.be/tRxGSHL8bI0?si=kbumCspOMG-kJvTT');

            $property->categories()->attach($categories->random(rand(1, 3)));
            $property->features()->attach($features->random(rand($featuresCount - 8, $featuresCount)));

            foreach (range(1, $facilitiesCount) as $facilityId) {
                $distance = sprintf('%skm', rand(1, 20));
                $property->facilities()->attach($facilityId, ['distance' => $distance]);
            }

            SlugHelper::createSlug($property);
        }
    }
}
