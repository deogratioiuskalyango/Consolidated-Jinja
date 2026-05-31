<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JinjaContactInfoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $phone = '0701 673401';
        $phoneInternational = '256701673401';
        $address = 'Clive Rd, Jinja, Uganda';
        $services = 'Land leasing and sales, new construction sales and leasing, and project management';

        foreach ([
            'theme-homzen-hotline' => $phone,
            'theme-homzen-whatsapp_phone_number' => $phoneInternational,
            'theme-homzen-whatsapp_inquiry_message' => 'Hello Jinja Consolidated Properties, I have an inquiry about this property: [property_url]',
            'theme-homzen-email' => '',
        ] as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => $now]
            );
        }

        DB::table('widgets')->where('id', 3)->update([
            'data' => json_encode([
                'about' => 'Jinja Consolidated Properties provides land leasing and sales, new construction sales and leasing, project management, rural rentals, rooms, and hardware marketplace support in Jinja.',
                'items' => [
                    [
                        ['key' => 'icon', 'value' => 'ti ti-map-pin'],
                        ['key' => 'text', 'value' => $address],
                    ],
                    [
                        ['key' => 'icon', 'value' => 'ti ti-phone-call'],
                        ['key' => 'text', 'value' => $phone],
                    ],
                    [
                        ['key' => 'icon', 'value' => 'ti ti-clock'],
                        ['key' => 'text', 'value' => 'Open 24 hours'],
                    ],
                    [
                        ['key' => 'icon', 'value' => 'ti ti-building-community'],
                        ['key' => 'text', 'value' => $services],
                    ],
                ],
            ]),
            'updated_at' => $now,
        ]);

        DB::table('widgets')->where('id', 4)->update([
            'data' => json_encode([
                'id' => 'Botble\\Widget\\Widgets\\CoreSimpleMenu',
                'name' => 'Services',
                'items' => [
                    $this->menuItem('Land Leasing & Sales', '/properties'),
                    $this->menuItem('New Construction Leasing', '/properties?type=rent'),
                    $this->menuItem('Project Management', '/contact-us'),
                    $this->menuItem('Hardware Quotations', '/hardware-quote'),
                ],
            ]),
            'updated_at' => $now,
        ]);

        DB::table('widgets')->where('id', 5)->update([
            'data' => json_encode([
                'id' => 'Botble\\Widget\\Widgets\\CoreSimpleMenu',
                'name' => 'Jinja Consolidated',
                'items' => [
                    $this->menuItem('Properties & Rooms', '/properties'),
                    $this->menuItem('Hardware Marketplace', '/products'),
                    $this->menuItem('Vendor Stores', '/stores'),
                    $this->menuItem('Contact & Directions', '/contact-us'),
                ],
            ]),
            'updated_at' => $now,
        ]);

        DB::table('widgets')->where('id', 6)->update([
            'data' => json_encode([
                'title' => 'Get Updates',
                'subtitle' => 'Property, land, room, construction, and hardware updates from Jinja Consolidated Properties.',
            ]),
            'updated_at' => $now,
        ]);

        $contactContent = '[contact-form display_fields="phone,email,subject,address" mandatory_fields="email" style="1" title="Contact Jinja Consolidated Properties" description="Reach us for land leasing and sales, new construction sales and leasing, project management, property rentals, rooms, and hardware marketplace support." show_information_box="1" contact_title="Contact Information" quantity="4" label_1="Address:" content_1="Clive Rd, Jinja, Uganda" label_2="Call:" content_2="0701 673401" label_3="Open time:" content_3="Open 24 hours" label_4="Services:" content_4="Land leasing and sales, new construction sales and leasing, and project management" show_social_links="1"][/contact-form]'
            . "\n[google-map]Clive Rd, Jinja, Uganda[/google-map]";

        DB::table('pages')->where('id', 7)->update([
            'name' => 'Contact Jinja Consolidated Properties',
            'description' => 'Contact Jinja Consolidated Properties on Clive Rd, Jinja for land leasing, sales, construction leasing, and project management.',
            'content' => $contactContent,
            'updated_at' => $now,
        ]);

        DB::table('slugs')->updateOrInsert(
            ['reference_id' => 7, 'reference_type' => 'Botble\Page\Models\Page', 'prefix' => ''],
            ['key' => 'contact-us', 'created_at' => $now, 'updated_at' => $now]
        );

        DB::table('re_accounts')->where('id', 13)->update([
            'phone' => $phone,
            'whatsapp' => $phoneInternational,
            'company' => 'Jinja Consolidated Properties',
            'description' => 'Official Jinja Consolidated Properties account for property rentals, land leasing, construction leasing, project management, and marketplace enquiries.',
            'updated_at' => $now,
        ]);
    }

    private function menuItem(string $label, string $url): array
    {
        return [
            ['key' => 'label', 'value' => $label],
            ['key' => 'url', 'value' => $url],
            ['key' => 'attributes', 'value' => ''],
            ['key' => 'is_open_new_tab', 'value' => '0'],
        ];
    }
}
