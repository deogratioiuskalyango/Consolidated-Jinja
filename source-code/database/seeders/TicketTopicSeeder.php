<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketTopicSeeder extends Seeder
{
    public function run(): void
    {
        $topics = [
            // ── Maintenance & Repairs ──────────────────────────────
            'Plumbing Issue',
            'Leaking Pipe or Tap',
            'Blocked Drain or Toilet',
            'Low Water Pressure',
            'Water Heater Problem',
            'Electrical Fault',
            'Power Outage in Unit',
            'Faulty Wiring or Sockets',
            'Circuit Breaker Tripping',
            'HVAC / Air Conditioning Fault',
            'Heating System Not Working',
            'Roof Leak or Water Seepage',
            'Ceiling Damage',
            'Wall Cracks or Structural Damage',
            'Flooring Damage',
            'Window Broken or Not Closing',
            'Door Lock or Handle Broken',
            'Sliding Door Problem',
            'Cabinet or Fixture Damage',
            'Painting or Wall Finish Issue',
            'Mold or Mildew in Unit',
            'Dampness or Water Stains',
            'Pest Infestation',
            'Termite Problem',
            'Rodent Issue',
            'Appliance Malfunction',
            'Built-in Oven / Stove Issue',
            'Dishwasher Problem',
            'Washing Machine Fault',
            'Refrigerator Not Cooling',

            // ── Utilities ─────────────────────────────────────────
            'Water Supply Interruption',
            'Electricity Supply Problem',
            'Gas Supply Issue',
            'Internet / Broadband Problem',
            'TV / Cable Service Issue',
            'Garbage Collection Missed',
            'Recycling Bin Request',
            'Utility Meter Reading Query',
            'Utility Bill Discrepancy',

            // ── Payments & Billing ────────────────────────────────
            'Payment Not Reflecting',
            'Invoice Dispute',
            'Receipt Request',
            'Refund Request',
            'Late Fee Waiver Request',
            'Security Deposit Query',
            'Billing Discrepancy',
            'Overpayment Adjustment',
            'Payment Plan Request',
            'Direct Debit Setup',

            // ── Lease & Tenancy ───────────────────────────────────
            'Lease Renewal Request',
            'Lease Termination Notice',
            'Early Termination Request',
            'Lease Agreement Query',
            'Subletting / Subleasing Request',
            'Adding Occupant to Lease',
            'Removing Occupant from Lease',
            'Rent Increase Dispute',
            'Rent Review Query',
            'Move-In Condition Report Query',
            'Move-Out Inspection Request',
            'Move-Out Date Change',

            // ── Security & Safety ─────────────────────────────────
            'Lost Key or Access Card',
            'Key Duplication Request',
            'Lock Change Request',
            'Intercom / Buzzer Not Working',
            'CCTV Concern',
            'Security Light Not Working',
            'Emergency Access Request',
            'Fire Safety Concern',
            'Smoke Alarm Fault',
            'Carbon Monoxide Alarm Issue',
            'Suspicious Activity Report',

            // ── Common Areas & Building ───────────────────────────
            'Common Area Cleanliness',
            'Corridor or Stairwell Issue',
            'Elevator / Lift Malfunction',
            'Parking Space Dispute',
            'Unauthorized Parking',
            'Parking Facility Damage',
            'Swimming Pool Issue',
            'Gym Equipment Fault',
            'Garden or Landscaping Concern',
            'Outdoor Lighting Problem',
            'Building Entrance Issue',
            'Mail / Parcel Delivery Problem',
            'Package Locker Fault',
            'Rooftop Access Issue',
            'Laundry Room Problem',
            'Storage Room Access',
            'Bicycle Storage Issue',

            // ── Noise & Neighbours ────────────────────────────────
            'Noise Complaint',
            'Neighbour Disturbance',
            'Neighbour Dispute',
            'Smoking in Non-Smoking Area',
            'Pet Nuisance Complaint',
            'Illegal Dumping or Littering',

            // ── Unit Requests ─────────────────────────────────────
            'Unit Inspection Request',
            'Deep Cleaning Request',
            'Furniture Removal Request',
            'Furniture Addition Request',
            'Additional Storage Request',
            'Renovation or Alteration Request',
            'Wall Hanging / Drilling Permission',
            'Air Conditioner Installation Request',
            'Pet Permission Request',

            // ── General Inquiries & Admin ─────────────────────────
            'General Inquiry',
            'Document or Certificate Request',
            'Letter of Residence Request',
            'Visitor Parking Pass Request',
            'Visitor Policy Query',
            'Community Rules Query',
            'Noise Policy Query',
            'Pet Policy Query',
            'Tenancy Policy Clarification',
            'Contact Details Update',
            'Emergency Contact Update',
            'Complaint About Staff',
            'Service Quality Feedback',
            'Compliment or Positive Feedback',
            'Other / Not Listed',
        ];

        $now = now();
        $rows = array_map(fn($name) => [
            'name'       => $name,
            'status'     => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ], $topics);

        // Insert in chunks to avoid query length limits; skip duplicates
        foreach (array_chunk($rows, 20) as $chunk) {
            DB::table('ticket_topics')->insertOrIgnore($chunk);
        }

        $this->command->info('Inserted ' . count($topics) . ' ticket topics.');
    }
}
