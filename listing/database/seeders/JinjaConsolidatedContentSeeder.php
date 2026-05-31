<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class JinjaConsolidatedContentSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $this->renamePlatform($now);
        $currencyId = $this->ensureCurrency('re_currencies', $now);
        $this->ensureCurrency('ec_currencies', $now);

        $blogCategoryId = $this->blogCategory('Uganda Rural Living', $now);
        $this->seedBlogPosts($blogCategoryId, $now);
        $this->seedProperties($currencyId, $now);
        $this->seedRooms($currencyId, $now);
        $stores = $this->seedStores($now);
        $this->seedProducts($stores, $now);
        $this->seedQuoteRequests($now);
    }

    private function renamePlatform($now): void
    {
        foreach ([
            'theme-homzen-site_title',
            'theme-homzen-seo_title',
            'admin_title',
            'site_title',
        ] as $key) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => 'Jinja Consolidated Properties', 'updated_at' => $now]
            );
        }

        $content = '[hero-banner style="1" title="Jinja Consolidated Properties" animation_text="Book rural stays,Rent rooms,Buy hardware,List your property" description="A Jinja-first marketplace for furnished rentals, rural homestays, rooms, hardware materials, quotations, and verified vendor stores across Uganda." background_image="pages/slider-1.jpg" search_box_enabled="1" projects_search_enabled="" default_search_type="property"][/hero-banner]'
            . "\r\n" . '[platform-hub title="Jinja rentals, rooms, and hardware marketplace" subtitle="Ugandan property and build supply" description="Rent homes and rooms, list spaces as a host, buy construction materials, request quotations, or open a vendor store with product, order, revenue, and withdrawal tools."][/platform-hub]'
            . "\r\n" . '[properties style="2" title="Jinja and rural Uganda rentals" subtitle="Book or enquire" type="rent" limit="6" button_label="Explore rentals" button_url="/properties" enable_lazy_loading="yes"][/properties]'
            . "\r\n" . '[location title="Explore by location" subtitle="Find stays near Jinja and Eastern Uganda" type="city" destination="property" background_color="#f7f7f7" enable_lazy_loading="yes"][/location]'
            . "\r\n" . '[services style="5" title="How the platform works" subtitle="Workflows" description="Jinja Consolidated Properties connects stays, property owners, hardware vendors, quotation requests, payments, vendor revenue, and admin moderation in one Botble platform." background_color="transparent" services_quantity="6" services_title_1="Book stays" services_description_1="Guests browse rentals and rooms, then enquire or book depending on listing type." services_icon_1="ti ti-bed" services_title_2="Host properties" services_description_2="Owners create an account, submit rental properties, track invoices, packages, and reviews." services_icon_2="ti ti-home-plus" services_title_3="Sell hardware" services_description_3="Suppliers become vendors and manage hardware products, orders, shipments, and revenues." services_icon_3="ti ti-tools" services_title_4="Request quotations" services_description_4="Builders submit a bill of quantities and the team matches it to hardware suppliers." services_icon_4="ti ti-file-invoice" services_title_5="Collect payments" services_description_5="Payment plugins support checkout, booking payments, vendor revenues, and withdrawals." services_icon_5="ti ti-credit-card" services_title_6="Admin moderation" services_description_6="Admins approve properties, vendors, products, accounts, rooms, bookings, and quotation requests." services_icon_6="ti ti-shield-check" counters_quantity="1" enable_lazy_loading="yes"][/services]'
            . "\r\n" . '[call-to-action title="Earn from your property or hardware store" subtitle="Become a partner" button_label="List a rental" button_url="/account/properties" image="pages/call-to-action.png" enable_lazy_loading="yes"][/call-to-action]';

        DB::table('pages')->where('id', 1)->update([
            'name' => 'Jinja Consolidated Properties',
            'description' => 'Jinja rentals, rural homestays, rooms, hardware ecommerce, marketplace vendors, and quotation requests.',
            'content' => $content,
            'updated_at' => $now,
        ]);
    }

    private function ensureCurrency(string $table, $now): int
    {
        if (! Schema::hasTable($table)) {
            return 1;
        }

        DB::table($table)->update(['is_default' => 0]);

        $id = DB::table($table)->where('title', 'UGS')->value('id');
        $payload = [
            'title' => 'UGS',
            'symbol' => 'UGS',
            'is_prefix_symbol' => 1,
            'decimals' => 0,
            'number_format_style' => 'western',
            'space_between_price_and_currency' => 1,
            'order' => 0,
            'is_default' => 1,
            'exchange_rate' => 1,
            'updated_at' => $now,
        ];

        if ($id) {
            DB::table($table)->where('id', $id)->update($payload);
            return (int) $id;
        }

        $payload['created_at'] = $now;

        return (int) DB::table($table)->insertGetId($payload);
    }

    private function blogCategory(string $name, $now): int
    {
        $id = DB::table('categories')->where('name', $name)->value('id');
        $payload = [
            'description' => 'Original guides for rural stays, Jinja rentals, construction planning, and hardware buying in Uganda.',
            'status' => 'published',
            'author_id' => 1,
            'author_type' => 'Botble\ACL\Models\User',
            'is_featured' => 1,
            'updated_at' => $now,
        ];

        if ($id) {
            DB::table('categories')->where('id', $id)->update($payload);
        } else {
            $payload += ['name' => $name, 'parent_id' => 0, 'order' => 0, 'is_default' => 0, 'created_at' => $now];
            $id = DB::table('categories')->insertGetId($payload);
        }

        $this->slug($name, (int) $id, 'Botble\Blog\Models\Category', 'news', $now);

        return (int) $id;
    }

    private function seedBlogPosts(int $categoryId, $now): void
    {
        $posts = [
            ['Jinja rural rentals: what guests look for near the Nile', 'Rural visitors often want clean rooms, reliable access, good directions, and a host who can explain transport to Jinja town, Bujagali, Itanda, and the Source of the Nile.', 'Use simple listing copy, honest photos, mosquito protection, safe water, clear house rules, and options for meals or local guides. The strongest rural stays combine privacy, cleanliness, and a practical link to nearby tourism, farms, river activities, or construction projects.'],
            ['Buying hardware for rural projects in Eastern Uganda', 'A practical checklist for cement, steel, roofing sheets, plumbing, electrical materials, and site delivery planning in rural builds.', 'For rural construction, the cheapest quote is rarely the best quote. Buyers should compare grades, delivery distance, offloading support, return policy, and whether the vendor can split delivery into foundation, walling, roofing, and finishing stages.'],
            ['Preparing a village home or farm stay for paying guests', 'Hosts can turn spare rooms, cottages, and family compounds into income if safety, hygiene, bedding, and communication are handled well.', 'The platform should encourage hosts to list room capacity, parking, security, cooking access, nearby trading centres, road condition, and whether guests can arrange boda, taxi, boat, or private vehicle pickup.'],
            ['Quotation checklist for cement, steel, plumbing, and solar', 'Builders can request a quotation faster when they attach a clear bill of quantities and explain delivery timing.', 'Good quotation requests include the site district, nearest landmark, estimated truck access, required brands or acceptable alternatives, contact person, payment schedule, and whether the buyer needs installation partners.'],
        ];

        foreach ($posts as [$name, $description, $body]) {
            $content = '<p>' . e($body) . '</p><p>This article was written for Jinja Consolidated Properties using public Uganda tourism and supplier context, then adapted into original platform guidance.</p>';
            $id = DB::table('posts')->where('name', $name)->value('id');
            $payload = [
                'description' => $description,
                'content' => $content,
                'status' => 'published',
                'author_id' => 1,
                'author_type' => 'Botble\ACL\Models\User',
                'is_featured' => 1,
                'format_type' => 'default',
                'updated_at' => $now,
            ];
            if ($id) {
                DB::table('posts')->where('id', $id)->update($payload);
            } else {
                $payload += ['name' => $name, 'views' => 0, 'created_at' => $now];
                $id = DB::table('posts')->insertGetId($payload);
            }
            DB::table('post_categories')->updateOrInsert(['post_id' => $id, 'category_id' => $categoryId]);
            $this->slug($name, (int) $id, 'Botble\Blog\Models\Post', 'news', $now);
        }
    }

    private function seedProperties(int $currencyId, $now): void
    {
        $images = json_encode(['properties/1.jpg', 'properties/2.jpg', 'properties/3.jpg']);
        $properties = [
            ['Source of the Nile Riverside Cottage', 'Jinja, near Source of the Nile', 220000, 2, 1, 95, 'Short stays'],
            ['Buwenda Farm Stay and Garden Home', 'Buwenda, Jinja District', 150000, 3, 2, 140, 'Serviced homes'],
            ['Njeru Worker Housing Compound', 'Njeru, Buikwe side of Jinja', 850000, 6, 4, 360, 'Apartments for rent'],
            ['Mafubira Family Rental Bungalow', 'Mafubira, Jinja City North', 650000, 3, 2, 180, 'House'],
            ['Budondo Rural Guest House Plot', 'Budondo, Jinja District', 350000, 4, 3, 420, 'Land and outdoor spaces'],
            ['Kakira Staff Accommodation Rooms', 'Kakira, Eastern Uganda', 280000, 8, 4, 300, 'Rooms for rent'],
        ];

        foreach ($properties as [$name, $location, $price, $beds, $baths, $square, $category]) {
            $id = DB::table('re_properties')->where('name', $name)->value('id');
            $content = '<p>' . e($name) . ' is a Jinja Consolidated Properties rental listing prepared for guests, workers, and families looking around Jinja and rural Eastern Uganda. The listing highlights practical access, host communication, clean rooms, and space for short or monthly stays.</p>';
            $payload = [
                'type' => 'rent',
                'description' => 'Uganda-focused rental listing for stays, rooms, work teams, or family accommodation around Jinja.',
                'content' => $content,
                'location' => $location,
                'images' => $images,
                'number_bedroom' => $beds,
                'number_bathroom' => $baths,
                'number_floor' => 1,
                'square' => $square,
                'price' => $price,
                'currency_id' => $currencyId,
                'is_featured' => 1,
                'period' => 'month',
                'status' => 'renting',
                'author_id' => 13,
                'author_type' => 'Botble\RealEstate\Models\Account',
                'moderation_status' => 'approved',
                'never_expired' => 1,
                'updated_at' => $now,
            ];
            if ($id) {
                DB::table('re_properties')->where('id', $id)->update($payload);
            } else {
                $payload += ['name' => $name, 'views' => 0, 'created_at' => $now];
                $id = DB::table('re_properties')->insertGetId($payload);
            }
            $categoryId = DB::table('re_categories')->where('name', $category)->value('id') ?: DB::table('re_categories')->where('name', 'House')->value('id');
            DB::table('re_property_categories')->updateOrInsert(['property_id' => $id, 'category_id' => $categoryId]);
            $this->slug($name, (int) $id, 'Botble\RealEstate\Models\Property', 'properties', $now);
        }
    }

    private function seedRooms(int $currencyId, $now): void
    {
        $rooms = [
            ['Nile View Private Room', 90000, 1, 1, 2, 'Private rooms'],
            ['Jinja Contractor Twin Room', 120000, 6, 2, 2, 'Shared stays'],
            ['Village Homestay Room', 70000, 2, 1, 2, 'Private rooms'],
            ['Family Suite near Jinja Town', 180000, 2, 3, 4, 'Entire suites'],
        ];

        foreach ($rooms as [$name, $price, $count, $beds, $adults, $category]) {
            $id = DB::table('ht_rooms')->where('name', $name)->value('id');
            $categoryId = DB::table('ht_room_categories')->where('name', $category)->value('id') ?: 1;
            $payload = [
                'description' => 'Bookable room content for Jinja Consolidated Properties guests and travelling work teams.',
                'content' => '<p>' . e($name) . ' is prepared for short stays, rural visits, project teams, and guests who want practical access to Jinja, Njeru, farms, and the Nile corridor.</p>',
                'is_featured' => 1,
                'images' => json_encode(['properties/4.jpg', 'properties/5.jpg']),
                'price' => $price,
                'currency_id' => $currencyId,
                'number_of_rooms' => $count,
                'number_of_beds' => $beds,
                'size' => 35,
                'max_adults' => $adults,
                'max_children' => 2,
                'room_category_id' => $categoryId,
                'status' => 'published',
                'updated_at' => $now,
            ];
            if ($id) {
                DB::table('ht_rooms')->where('id', $id)->update($payload);
            } else {
                $payload += ['name' => $name, 'order' => 0, 'created_at' => $now];
                $id = DB::table('ht_rooms')->insertGetId($payload);
            }
            $this->slug($name, (int) $id, 'Botble\Hotel\Models\Room', 'rooms', $now);
        }
    }

    private function seedStores($now): array
    {
        $stores = [
            ['Jinja Consolidated Hardware Depot', 'hardware@jinjaconproperties.test', 'Jinja City', 'Local building materials, cement, roofing, plumbing, and quotation support.'],
            ['Nile Valley Building Supplies', 'nilevalley@jinjaconproperties.test', 'Njeru and Jinja', 'Steel, roofing sheets, aggregates, tools, and site delivery coordination.'],
            ['Eastern Uganda Tools and Plumbing', 'easterntools@jinjaconproperties.test', 'Mafubira, Jinja', 'Plumbing, electrical, hand tools, finishing materials, and repair items.'],
            ['China Uganda Equipment Supply Desk', 'chinaequipment@jinjaconproperties.test', 'Kampala to Jinja delivery', 'Chinese-manufactured construction equipment leads, spare parts requests, compact machinery, and quote coordination.'],
            ['XCMG Nile Equipment Desk', 'xcmgdesk@jinjaconproperties.test', 'Uganda equipment supply', 'Marketplace desk for XCMG-style road, lifting, earthmoving, and spare-parts enquiries in Uganda.'],
        ];

        $ids = [];
        foreach ($stores as [$name, $email, $city, $description]) {
            $customerId = DB::table('ec_customers')->where('email', $email)->value('id');
            if (! $customerId) {
                $customerId = DB::table('ec_customers')->insertGetId([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('Password123!'),
                    'phone' => '+256700000000',
                    'confirmed_at' => $now,
                    'status' => 'activated',
                    'is_vendor' => 1,
                    'vendor_verified_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } else {
                DB::table('ec_customers')->where('id', $customerId)->update(['is_vendor' => 1, 'status' => 'activated', 'vendor_verified_at' => $now, 'updated_at' => $now]);
            }

            $storeId = DB::table('mp_stores')->where('name', $name)->value('id');
            $payload = [
                'email' => $email,
                'phone' => '+256700000000',
                'address' => $city,
                'country' => 'Uganda',
                'state' => 'Eastern Region',
                'city' => $city,
                'customer_id' => $customerId,
                'description' => $description,
                'content' => '<p>' . e($description) . ' Listings are curated for English-language buyers using UGS pricing and quote-first workflows for bulk supply.</p>',
                'status' => 'published',
                'is_verified' => 1,
                'verified_at' => $now,
                'verified_by' => 1,
                'vendor_verified_at' => $now,
                'updated_at' => $now,
            ];
            if ($storeId) {
                DB::table('mp_stores')->where('id', $storeId)->update($payload);
            } else {
                $payload += ['name' => $name, 'created_at' => $now];
                $storeId = DB::table('mp_stores')->insertGetId($payload);
            }
            $ids[$name] = (int) $storeId;
        }

        return $ids;
    }

    private function seedProducts(array $stores, $now): void
    {
        $products = [
            ['Tororo-style Portland Cement 50kg', 'Building materials', 38000, 500, 'Jinja Consolidated Hardware Depot'],
            ['12mm Deformed Reinforcement Bar', 'Building materials', 42000, 350, 'Nile Valley Building Supplies'],
            ['Gauge 28 Pre-painted Roofing Sheet', 'Doors and windows', 32000, 400, 'Nile Valley Building Supplies'],
            ['PVC Plumbing Pipe 1 inch', 'Plumbing', 18000, 600, 'Eastern Uganda Tools and Plumbing'],
            ['Twin and Earth Electrical Cable Roll', 'Electrical', 165000, 80, 'Eastern Uganda Tools and Plumbing'],
            ['Solar LED Flood Light 100W', 'Electrical', 95000, 120, 'Jinja Consolidated Hardware Depot'],
            ['Exterior Weather Guard Paint 20L', 'Finishes and paint', 185000, 70, 'Eastern Uganda Tools and Plumbing'],
            ['Contractor Wheelbarrow Heavy Duty', 'Tools and equipment', 210000, 45, 'Jinja Consolidated Hardware Depot'],
            ['Ceramic Floor Tiles Carton', 'Finishes and paint', 58000, 250, 'Nile Valley Building Supplies'],
            ['Compact Excavator Quote Lead', 'Tools and equipment', 185000000, 3, 'China Uganda Equipment Supply Desk'],
            ['XCMG-style Motor Grader Quote Lead', 'Tools and equipment', 420000000, 2, 'XCMG Nile Equipment Desk'],
            ['Concrete Mixer and Pump Quote Lead', 'Tools and equipment', 260000000, 2, 'China Uganda Equipment Supply Desk'],
        ];

        foreach ($products as [$name, $category, $price, $qty, $storeName]) {
            $storeId = $stores[$storeName] ?? reset($stores);
            $slug = Str::slug($name);
            $id = DB::table('ec_products')->where('name', $name)->value('id');
            $payload = [
                'slug' => $slug,
                'description' => 'UGS-priced marketplace listing for Ugandan construction and rural project buyers.',
                'content' => '<p>' . e($name) . ' is listed for Jinja Consolidated Properties buyers who need clear English descriptions, UGS pricing, and quotation support for delivery around Jinja and wider Uganda.</p>',
                'status' => 'published',
                'images' => json_encode([]),
                'sku' => strtoupper(Str::slug($name, '-')),
                'order' => 0,
                'quantity' => $qty,
                'allow_checkout_when_out_of_stock' => 0,
                'with_storehouse_management' => 1,
                'stock_status' => 'in_stock',
                'is_featured' => 1,
                'price' => $price,
                'sale_price' => null,
                'created_by_id' => 1,
                'created_by_type' => 'Botble\ACL\Models\User',
                'product_type' => 'physical',
                'store_id' => $storeId,
                'approved_by' => 1,
                'updated_at' => $now,
            ];
            if ($id) {
                DB::table('ec_products')->where('id', $id)->update($payload);
            } else {
                $payload += ['name' => $name, 'views' => 0, 'created_at' => $now];
                $id = DB::table('ec_products')->insertGetId($payload);
            }
            $categoryId = DB::table('ec_product_categories')->where('name', $category)->value('id');
            if ($categoryId) {
                DB::table('ec_product_category_product')->updateOrInsert(['product_id' => $id, 'category_id' => $categoryId]);
            }
            $this->slug($name, (int) $id, 'Botble\Ecommerce\Models\Product', 'products', $now);
        }
    }

    private function seedQuoteRequests($now): void
    {
        if (! Schema::hasTable('hardware_quote_requests')) {
            return;
        }

        foreach ([
            ['Buwenda farm cottage roofing package', 'roofing', 'Buwenda, Jinja District', 'Cement, timber, gauge 28 sheets, ridges, nails, gutters, delivery truck access notes.', 4500000],
            ['Njeru worker housing plumbing and electrical', 'worker housing', 'Njeru industrial area', 'PVC pipes, sockets, switches, lighting, cable rolls, breakers, water fittings.', 7200000],
        ] as [$name, $type, $location, $materials, $budget]) {
            DB::table('hardware_quote_requests')->updateOrInsert(
                ['email' => Str::slug($name) . '@example.test'],
                [
                    'name' => $name,
                    'phone' => '+256700000000',
                    'project_type' => $type,
                    'delivery_location' => $location,
                    'needed_by' => now()->addWeeks(3)->toDateString(),
                    'materials' => $materials,
                    'budget' => $budget,
                    'status' => 'new',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    private function slug(string $name, int $id, string $type, string $prefix, $now): void
    {
        DB::table('slugs')->updateOrInsert(
            ['reference_id' => $id, 'reference_type' => $type, 'prefix' => $prefix],
            ['key' => Str::slug($name), 'updated_at' => $now, 'created_at' => $now]
        );
    }
}
