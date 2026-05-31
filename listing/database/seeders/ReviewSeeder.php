<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Models\Review;
use Carbon\Carbon;

class ReviewSeeder extends BaseSeeder
{
    protected array $propertyReviews = [
        'Absolutely loved this property! The location is perfect for commuting to work, and the neighborhood is very safe and friendly. Would highly recommend to anyone looking for a great place to live.',
        'Great property with modern amenities. The kitchen is spacious and well-equipped. Only wish the parking area was a bit larger.',
        'Excellent value for money. The property exceeded my expectations in terms of quality and comfort. The landlord was very responsive and helpful throughout.',
        'Beautiful home with amazing natural light. The garden is well-maintained and perfect for weekend barbecues. Very happy with my decision.',
        'The property is well-maintained and in a prime location. Close to schools, shopping centers, and public transportation. Perfect for families.',
        'Stunning views from the balcony. The interior design is modern and tasteful. Highly recommend for professionals seeking a quiet retreat.',
        'Spacious rooms and excellent storage space. The property has been recently renovated and everything looks brand new.',
        'Perfect starter home for young couples. Affordable yet comfortable with all necessary amenities. Great community atmosphere.',
        'The property photos were accurate and the space is even better in person. Move-in ready and professionally cleaned.',
        'Outstanding property management. Any issues were addressed promptly. The security features give peace of mind.',
        'Loved the open floor plan and high ceilings. Natural ventilation is excellent, reducing energy costs significantly.',
        'Prime location near downtown but surprisingly quiet. Best of both worlds - urban convenience with suburban peace.',
        'The attention to detail in this property is impressive. Quality fixtures and finishes throughout.',
        'Wonderful experience from viewing to move-in. The agent was professional and answered all my questions thoroughly.',
        'Great investment property. Already seeing good returns. Location ensures consistent demand from tenants.',
        'The neighborhood is vibrant with lots of cafes and restaurants nearby. Perfect for young professionals.',
        'Family-friendly community with excellent schools nearby. Safe streets for children to play.',
        'Modern smart home features throughout. Energy-efficient appliances save on monthly bills.',
        'Generous closet space and built-in storage solutions. Perfect for those who value organization.',
        'The property has character and charm while still offering modern conveniences. Best of both worlds.',
        'Exceptional customer service from the real estate team. They made the entire process smooth and stress-free.',
        'Great natural lighting throughout the day. The south-facing windows make a huge difference.',
        'Well-insulated property keeps energy costs low. Comfortable in both summer and winter.',
        'The community amenities are fantastic - pool, gym, and clubhouse all well-maintained.',
        'Quiet neighbors and a peaceful atmosphere. Perfect for working from home.',
        'The layout is practical and space-efficient. Every square foot is well utilized.',
        'High-quality construction materials visible throughout. This property was built to last.',
        'Love the outdoor space - perfect for gardening and entertaining guests.',
        'Central location means everything is just minutes away. Very convenient for daily errands.',
        'The property has great resale potential. Smart investment for the future.',
    ];

    protected array $projectReviews = [
        'This development project has exceeded all expectations. The architects have done an amazing job balancing aesthetics with functionality.',
        'Impressive project with top-notch facilities. The developers have clearly prioritized quality over quantity.',
        'The project timeline was met and the final result is stunning. Very professional team behind this development.',
        'Great investment opportunity. The project is in a rapidly developing area with strong growth potential.',
        'The amenities included in this project are world-class. Swimming pool, gym, and community spaces are all beautifully designed.',
        'Sustainable building practices were used throughout this project. Appreciate the eco-friendly approach.',
        'The common areas in this project are exceptionally well-designed. Perfect blend of functionality and beauty.',
        'Project management was transparent throughout the development phase. Regular updates kept buyers informed.',
        'High-quality materials used in construction. You can see the attention to detail in every corner.',
        'The landscaping around this project is beautiful. Green spaces make the community feel alive.',
        'Security features throughout the project are state-of-the-art. Feel safe living here.',
        'The project offers great variety in unit sizes. Something for everyone - singles, couples, and families.',
        'Parking facilities are well-planned with ample space for residents and visitors.',
        'The project has excellent connectivity to major roads and public transportation.',
        'Developer reputation speaks for itself. This project maintains their high standards.',
        'The rooftop amenities are a standout feature of this project. Great views and relaxation spaces.',
        'Child-friendly design throughout the project with safe play areas and family amenities.',
        'The project completion ahead of schedule shows excellent project management.',
        'Energy-efficient design reduces monthly utility costs significantly.',
        'The fitness center in this project rivals professional gyms. Very well-equipped.',
        'Smart home integration throughout the project. Modern living at its finest.',
        'The developer provided excellent after-sales service. Any snags were fixed promptly.',
        'Beautiful architectural design that stands out in the neighborhood.',
        'The project has enhanced property values in the entire area.',
        'Community events organized by the management create a wonderful neighborhood atmosphere.',
        'Sound insulation between units is excellent. No noise complaints whatsoever.',
        'The lobby and entrance areas make a great first impression on visitors.',
        'Pet-friendly project with designated areas for dogs and cats.',
        'The project has won multiple awards for its design and sustainability features.',
        'Looking forward to the next phase of this development. The first phase has been outstanding.',
    ];

    public function run(): void
    {
        Review::query()->truncate();

        $accounts = Account::query()->pluck('id');
        $projects = Project::query()->pluck('id');
        $properties = Property::query()->pluck('id');

        $now = Carbon::now();

        for ($i = 1; $i <= count($properties) * 10; $i++) {
            Review::query()->insertOrIgnore([
                'id' => (new Review())->newUniqueId(),
                'account_id' => $accounts->random(),
                'reviewable_type' => Property::class,
                'reviewable_id' => $properties->random(),
                'content' => $this->propertyReviews[array_rand($this->propertyReviews)],
                'star' => rand(1, 5),
                'created_at' => Carbon::now()->subDays(rand(0, 120)),
                'updated_at' => $now,
            ]);

            Review::query()->insertOrIgnore([
                'id' => (new Review())->newUniqueId(),
                'account_id' => $accounts->random(),
                'reviewable_type' => Project::class,
                'reviewable_id' => $projects->random(),
                'content' => $this->projectReviews[array_rand($this->projectReviews)],
                'star' => rand(1, 5),
                'created_at' => Carbon::now()->subDays(rand(0, 120)),
                'updated_at' => $now,
            ]);
        }
    }
}
