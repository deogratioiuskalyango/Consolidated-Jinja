<?php

namespace Database\Seeders;

use Botble\Base\Facades\MetaBox;
use Botble\Base\Supports\BaseSeeder;
use Botble\RealEstate\Models\Account;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountSeeder extends BaseSeeder
{
    public function run(): void
    {
        Account::query()->truncate();

        $files = $this->uploadFiles('avatars');

        $now = $this->now();

        $socialLinks = [
            [
                [
                    'key' => 'name',
                    'value' => 'Facebook',
                ],
                [
                    'key' => 'icon',
                    'value' => 'ti ti-brand-facebook',
                ],
                [
                    'key' => 'url',
                    'value' => 'https://www.facebook.com/',
                ],
            ],
            [
                [
                    'key' => 'name',
                    'value' => 'Instagram',
                ],
                [
                    'key' => 'icon',
                    'value' => 'ti ti-brand-instagram',
                ],
                [
                    'key' => 'url',
                    'value' => 'https://www.instagram.com/',
                ],
            ],
            [
                [
                    'key' => 'name',
                    'value' => 'Twitter',
                ],
                [
                    'key' => 'icon',
                    'value' => 'ti ti-brand-x',
                ],
                [
                    'key' => 'url',
                    'value' => 'https://www.twitter.com/',
                ],
            ],
            [
                [
                    'key' => 'name',
                    'value' => 'YouTube',
                ],
                [
                    'key' => 'icon',
                    'value' => 'ti ti-brand-youtube',
                ],
                [
                    'key' => 'url',
                    'value' => 'https://www.youtube.com/',
                ],
            ],
        ];

        $agentDescriptions = [
            'Dedicated real estate professional with expertise in residential properties.',
            'Experienced agent specializing in luxury homes and investment properties.',
            'Passionate about helping clients find their perfect home.',
            'Top-performing agent with strong negotiation skills.',
            'Local market expert committed to exceptional client service.',
            'Full-service real estate professional for buyers and sellers.',
            'Trusted advisor with deep knowledge of the local market.',
            'Results-driven agent focused on client satisfaction.',
            'Seasoned professional with extensive market knowledge.',
            'Customer-focused agent delivering outstanding results.',
            'Skilled negotiator with a proven track record of success.',
            'Committed to making your real estate dreams a reality.',
        ];

        $firstNames = ['John', 'Sarah', 'Michael', 'Emily', 'David', 'Jennifer', 'Robert', 'Lisa', 'James', 'Amanda', 'William', 'Jessica'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Wilson', 'Anderson'];
        $phones = ['+14155551234', '+12125559876', '+13105557890', '+17185554321', '+16505558765', '+16465553456', '+14085552468', '+15105551357', '+16195559630', '+19495558520', '+13235557410', '+16265556320'];

        $emails = ['john.smith@botble.com', 'agent@botble.com'];

        foreach ($emails as $index => $email) {
            $isVerified = $index === 0;

            $data = [
                'first_name' => $firstNames[$index],
                'last_name' => $lastNames[$index],
                'email' => $email,
                'username' => Str::slug($firstNames[$index] . '-' . $lastNames[$index] . '-' . rand(100, 999)),
                'password' => Hash::make('12345678'),
                'dob' => Carbon::now()->subYears(rand(25, 55))->subDays(rand(0, 365)),
                'phone' => $phone = $phones[$index],
                'whatsapp' => $phone,
                'description' => $agentDescriptions[array_rand($agentDescriptions)],
                'credits' => 10,
                'confirmed_at' => $now,
                'approved_at' => $now,
                'avatar_id' => $files[array_rand($files)]['data']->id,
                'is_public_profile' => true,
                'is_verified' => $isVerified,
            ];

            if ($isVerified) {
                $data['verified_at'] = Carbon::now()->subDays(rand(1, 365));
                $data['verified_by'] = 1;
                $data['verification_note'] = 'Verified trusted agent';
            }

            $account = Account::query()->create($data);

            MetaBox::saveMetaBoxData($account, 'social_links', $socialLinks);
        }

        $verificationNotes = [
            'Verified after background check',
            'Documents verified successfully',
            'Agent credentials confirmed',
            'Verified trusted partner',
            'Premium agent - verified',
            null,
        ];

        foreach (range(1, 10) as $index) {
            $isVerified = rand(0, 100) < 40;
            $nameIndex = ($index + 1) % count($firstNames);

            $data = [
                'first_name' => $firstNames[$nameIndex],
                'last_name' => $lastNames[($nameIndex + 3) % count($lastNames)],
                'email' => strtolower($firstNames[$nameIndex]) . '.' . strtolower($lastNames[($nameIndex + 3) % count($lastNames)]) . $index . '@example.com',
                'username' => Str::slug($firstNames[$nameIndex] . '-' . $lastNames[($nameIndex + 3) % count($lastNames)] . '-' . rand(100, 999)),
                'password' => Hash::make('password123'),
                'dob' => Carbon::now()->subYears(rand(25, 55))->subDays(rand(0, 365)),
                'phone' => $phone = $phones[$index % count($phones)],
                'whatsapp' => $phone,
                'description' => $agentDescriptions[array_rand($agentDescriptions)],
                'credits' => rand(1, 10),
                'confirmed_at' => $now,
                'approved_at' => $now,
                'avatar_id' => $files[array_rand($files)]['data']->id,
                'is_public_profile' => true,
                'is_featured' => (bool) rand(0, 1),
                'is_verified' => $isVerified,
            ];

            if ($isVerified) {
                $data['verified_at'] = Carbon::now()->subDays(rand(1, 365));
                $data['verified_by'] = 1;
                $data['verification_note'] = $verificationNotes[array_rand($verificationNotes)];
            }

            $account = Account::query()->create($data);

            MetaBox::saveMetaBoxData($account, 'social_links', $socialLinks);
        }
    }
}
