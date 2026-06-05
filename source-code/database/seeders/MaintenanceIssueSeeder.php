<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaintenanceIssueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = now();

        $issues = [
            // ── Plumbing ────────────────────────────────────────────────
            ['category' => 'Plumbing',     'name' => 'Leaking tap / faucet'],
            ['category' => 'Plumbing',     'name' => 'Blocked kitchen sink'],
            ['category' => 'Plumbing',     'name' => 'Blocked bathroom drain'],
            ['category' => 'Plumbing',     'name' => 'Blocked toilet'],
            ['category' => 'Plumbing',     'name' => 'No hot water'],
            ['category' => 'Plumbing',     'name' => 'Burst or leaking pipe'],
            ['category' => 'Plumbing',     'name' => 'Low water pressure'],
            ['category' => 'Plumbing',     'name' => 'Running / constantly flushing toilet'],
            ['category' => 'Plumbing',     'name' => 'Leaking shower or bath'],
            ['category' => 'Plumbing',     'name' => 'Sewage or drain odour'],
            ['category' => 'Plumbing',     'name' => 'Water heater failure'],
            ['category' => 'Plumbing',     'name' => 'Overflowing sink or bathtub'],

            // ── Electrical ──────────────────────────────────────────────
            ['category' => 'Electrical',   'name' => 'No power in unit'],
            ['category' => 'Electrical',   'name' => 'Broken / faulty power outlet'],
            ['category' => 'Electrical',   'name' => 'Flickering or dead lights'],
            ['category' => 'Electrical',   'name' => 'Tripped circuit breaker'],
            ['category' => 'Electrical',   'name' => 'Broken light switch'],
            ['category' => 'Electrical',   'name' => 'Exposed or damaged wiring'],
            ['category' => 'Electrical',   'name' => 'Sparking outlet'],
            ['category' => 'Electrical',   'name' => 'No power to specific room'],
            ['category' => 'Electrical',   'name' => 'Outdoor lights not working'],
            ['category' => 'Electrical',   'name' => 'Intercom / doorbell not working'],
            ['category' => 'Electrical',   'name' => 'Meter box issue'],

            // ── HVAC / Air Con ──────────────────────────────────────────
            ['category' => 'HVAC & Climate', 'name' => 'Air conditioner not cooling'],
            ['category' => 'HVAC & Climate', 'name' => 'Air conditioner not heating'],
            ['category' => 'HVAC & Climate', 'name' => 'AC leaking water'],
            ['category' => 'HVAC & Climate', 'name' => 'Noisy AC unit'],
            ['category' => 'HVAC & Climate', 'name' => 'Thermostat not working'],
            ['category' => 'HVAC & Climate', 'name' => 'No ventilation / poor airflow'],
            ['category' => 'HVAC & Climate', 'name' => 'Heater not working'],
            ['category' => 'HVAC & Climate', 'name' => 'Ceiling fan broken'],
            ['category' => 'HVAC & Climate', 'name' => 'Exhaust fan not working'],
            ['category' => 'HVAC & Climate', 'name' => 'Mold or mildew in AC vents'],
            ['category' => 'HVAC & Climate', 'name' => 'AC remote not responding'],

            // ── Structural ──────────────────────────────────────────────
            ['category' => 'Structural',   'name' => 'Cracked wall or ceiling'],
            ['category' => 'Structural',   'name' => 'Roof leak / water ingress'],
            ['category' => 'Structural',   'name' => 'Broken or cracked window glass'],
            ['category' => 'Structural',   'name' => 'Window won\'t open or close properly'],
            ['category' => 'Structural',   'name' => 'Broken door'],
            ['category' => 'Structural',   'name' => 'Door won\'t close or align properly'],
            ['category' => 'Structural',   'name' => 'Ceiling damage or sagging'],
            ['category' => 'Structural',   'name' => 'Balcony or railing damage'],
            ['category' => 'Structural',   'name' => 'Damp or water stain on wall'],
            ['category' => 'Structural',   'name' => 'Subsidence or foundation concern'],
            ['category' => 'Structural',   'name' => 'Sliding door off track'],

            // ── Appliances ──────────────────────────────────────────────
            ['category' => 'Appliances',   'name' => 'Refrigerator not working'],
            ['category' => 'Appliances',   'name' => 'Oven or stove not working'],
            ['category' => 'Appliances',   'name' => 'Dishwasher not working'],
            ['category' => 'Appliances',   'name' => 'Washing machine broken or leaking'],
            ['category' => 'Appliances',   'name' => 'Dryer not working'],
            ['category' => 'Appliances',   'name' => 'Microwave not working'],
            ['category' => 'Appliances',   'name' => 'Garbage disposal broken'],
            ['category' => 'Appliances',   'name' => 'Range hood not working'],
            ['category' => 'Appliances',   'name' => 'Hot water system failure'],
            ['category' => 'Appliances',   'name' => 'Cooktop ignition not working'],

            // ── Pest Control ────────────────────────────────────────────
            ['category' => 'Pest Control', 'name' => 'Cockroach infestation'],
            ['category' => 'Pest Control', 'name' => 'Rodents (mice or rats)'],
            ['category' => 'Pest Control', 'name' => 'Ant infestation'],
            ['category' => 'Pest Control', 'name' => 'Bed bug infestation'],
            ['category' => 'Pest Control', 'name' => 'Spider infestation'],
            ['category' => 'Pest Control', 'name' => 'Termites / white ants'],
            ['category' => 'Pest Control', 'name' => 'Fly infestation'],
            ['category' => 'Pest Control', 'name' => 'Wasp or bee nest'],
            ['category' => 'Pest Control', 'name' => 'Mosquito problem'],
            ['category' => 'Pest Control', 'name' => 'Other pest infestation'],

            // ── Security ────────────────────────────────────────────────
            ['category' => 'Security',     'name' => 'Broken door lock'],
            ['category' => 'Security',     'name' => 'Lost key or locked out'],
            ['category' => 'Security',     'name' => 'Broken door handle or knob'],
            ['category' => 'Security',     'name' => 'Security alarm fault'],
            ['category' => 'Security',     'name' => 'CCTV camera not working'],
            ['category' => 'Security',     'name' => 'Broken garage or roller door'],
            ['category' => 'Security',     'name' => 'Broken security screen or fly screen'],
            ['category' => 'Security',     'name' => 'Window lock broken'],
            ['category' => 'Security',     'name' => 'Deadbolt not functioning'],
            ['category' => 'Security',     'name' => 'Access card or fob not working'],

            // ── Flooring ────────────────────────────────────────────────
            ['category' => 'Flooring',     'name' => 'Cracked or broken tiles'],
            ['category' => 'Flooring',     'name' => 'Damaged floorboards'],
            ['category' => 'Flooring',     'name' => 'Carpet stain or tear'],
            ['category' => 'Flooring',     'name' => 'Loose or lifting flooring'],
            ['category' => 'Flooring',     'name' => 'Squeaky floorboards'],
            ['category' => 'Flooring',     'name' => 'Slippery floor surface'],
            ['category' => 'Flooring',     'name' => 'Grout damaged or missing'],
            ['category' => 'Flooring',     'name' => 'Vinyl or laminate peeling'],
            ['category' => 'Flooring',     'name' => 'Water-damaged floor'],

            // ── Painting & Walls ────────────────────────────────────────
            ['category' => 'Painting & Walls', 'name' => 'Peeling or flaking paint'],
            ['category' => 'Painting & Walls', 'name' => 'Mold or mildew on wall'],
            ['category' => 'Painting & Walls', 'name' => 'Water stain on wall or ceiling'],
            ['category' => 'Painting & Walls', 'name' => 'Cracked plaster or render'],
            ['category' => 'Painting & Walls', 'name' => 'Graffiti or vandalism damage'],
            ['category' => 'Painting & Walls', 'name' => 'Wallpaper peeling'],
            ['category' => 'Painting & Walls', 'name' => 'Discolouration or staining'],
            ['category' => 'Painting & Walls', 'name' => 'Hole in wall'],

            // ── Common Areas ────────────────────────────────────────────
            ['category' => 'Common Areas', 'name' => 'Lift / elevator not working'],
            ['category' => 'Common Areas', 'name' => 'Lobby or corridor light out'],
            ['category' => 'Common Areas', 'name' => 'Letterbox damaged or locked'],
            ['category' => 'Common Areas', 'name' => 'Bin or waste area issue'],
            ['category' => 'Common Areas', 'name' => 'Roof leak in common area'],
            ['category' => 'Common Areas', 'name' => 'Fire door not closing properly'],
            ['category' => 'Common Areas', 'name' => 'Gym equipment broken'],
            ['category' => 'Common Areas', 'name' => 'Pool or spa issue'],
            ['category' => 'Common Areas', 'name' => 'Car park gate not working'],
            ['category' => 'Common Areas', 'name' => 'Laundry room issue'],
            ['category' => 'Common Areas', 'name' => 'Common area flooding'],

            // ── Outdoor & Garden ────────────────────────────────────────
            ['category' => 'Outdoor & Garden', 'name' => 'Broken garden tap or hose'],
            ['category' => 'Outdoor & Garden', 'name' => 'Overgrown garden or trees'],
            ['category' => 'Outdoor & Garden', 'name' => 'Damaged or broken fence'],
            ['category' => 'Outdoor & Garden', 'name' => 'Broken gate or entry'],
            ['category' => 'Outdoor & Garden', 'name' => 'Patio or deck damage'],
            ['category' => 'Outdoor & Garden', 'name' => 'Garden or path light broken'],
            ['category' => 'Outdoor & Garden', 'name' => 'Irrigation system fault'],
            ['category' => 'Outdoor & Garden', 'name' => 'Driveway or path damage'],
            ['category' => 'Outdoor & Garden', 'name' => 'Tree branch hazard'],
            ['category' => 'Outdoor & Garden', 'name' => 'Outdoor furniture damage'],

            // ── General ─────────────────────────────────────────────────
            ['category' => 'General',      'name' => 'Unpleasant odour in unit'],
            ['category' => 'General',      'name' => 'Noise from neighbouring unit'],
            ['category' => 'General',      'name' => 'Smoke or fire alarm issue'],
            ['category' => 'General',      'name' => 'Carbon monoxide detector fault'],
            ['category' => 'General',      'name' => 'Storage room issue'],
            ['category' => 'General',      'name' => 'Internet or TV connection issue'],
            ['category' => 'General',      'name' => 'Rubbish chute blocked'],
            ['category' => 'General',      'name' => 'Package or post box issue'],
            ['category' => 'General',      'name' => 'Other maintenance issue'],
        ];

        $rows = array_map(fn($i) => array_merge($i, [
            'owner_user_id' => null,   // global — visible to all owners/tenants
            'status'        => 1,      // ACTIVE
            'created_at'    => $now,
            'updated_at'    => $now,
        ]), $issues);

        foreach (array_chunk($rows, 25) as $chunk) {
            \Illuminate\Support\Facades\DB::table('maintenance_issues')->insertOrIgnore($chunk);
        }

        $this->command->info('Seeded ' . count($rows) . ' maintenance issues across 12 categories.');
    }
}
