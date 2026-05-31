<?php

namespace Database\Seeders;

use ArchiElite\Career\Models\Career;
use Botble\Base\Facades\MetaBox;
use Botble\Base\Supports\BaseSeeder;
use Botble\Slug\Facades\SlugHelper;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class CareerSeeder extends BaseSeeder
{
    public function run(): void
    {
        Career::query()->truncate();

        $careers = [
            [
                'name' => 'Senior Full Stack Engineer, Creator Success Full Time',
                'description' => 'Lead development of innovative real estate solutions using modern technologies',
                'location' => 'San Francisco, United States',
                'image' => 'general/job-details-thumb.png',
            ],
            [
                'name' => 'Data Science Specialist, Analytics Division',
                'description' => 'Drive data-driven decisions through advanced analytics and machine learning models',
                'location' => 'New York, United States',
                'image' => 'general/job-details-thumb.png',
            ],
            [
                'name' => 'Product Marketing Manager, Growth Team',
                'description' => 'Craft compelling marketing strategies to drive user acquisition and brand growth',
                'location' => 'Los Angeles, United States',
                'image' => 'general/job-details-thumb.png',
            ],
            [
                'name' => 'UX/UI Designer, User Experience Team',
                'description' => 'Design intuitive user interfaces that deliver exceptional customer experiences',
                'location' => 'Seattle, United States',
                'image' => 'general/job-details-thumb.png',
            ],
            [
                'name' => 'Operations Manager, Supply Chain Division',
                'description' => 'Optimize operational workflows and ensure seamless business processes',
                'location' => 'Chicago, United States',
                'image' => 'general/job-details-thumb.png',
            ],
            [
                'name' => 'Financial Analyst, Investment Group',
                'description' => 'Analyze market trends and investment opportunities for strategic decision-making',
                'location' => 'Boston, United States',
                'image' => 'general/job-details-thumb.png',
            ],
        ];

        $salaries = ['$50,000 - $80,000', '$70,000 - $100,000', '$80,000 - $120,000', '$90,000 - $130,000', '$100,000 - $150,000', '$60,000 - $90,000'];

        foreach ($careers as $index => $item) {
            $index++;

            $career = Career::query()->create(array_merge(Arr::except($item, ['image', 'icon']), [
                'salary' => $salaries[array_rand($salaries)],
                'content' => File::get(database_path('/seeders/contents/career-detail.html')),
            ]));

            MetaBox::saveMetaBoxData($career, 'image', $item['image']);
            MetaBox::saveMetaBoxData($career, 'icon', "icons/icon$index.png");
            MetaBox::saveMetaBoxData($career, 'apply_url', '/contact');

            SlugHelper::createSlug($career);
        }
    }
}
