<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KycConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = now();

        // is_both: 1 = requires front + back side, 0 = front side only
        $configs = [
            // ── Identity Documents ──────────────────────────────────────
            ['category' => 'Identity',      'name' => 'National ID Card',              'is_both' => 1, 'details' => 'Government-issued national identity card (front and back required).'],
            ['category' => 'Identity',      'name' => 'Passport',                      'is_both' => 0, 'details' => 'Valid passport biographical page.'],
            ['category' => 'Identity',      'name' => 'Driver\'s License',             'is_both' => 1, 'details' => 'Government-issued driver\'s license (front and back required).'],
            ['category' => 'Identity',      'name' => 'Birth Certificate',             'is_both' => 0, 'details' => 'Official birth certificate issued by a government authority.'],
            ['category' => 'Identity',      'name' => 'Voter ID Card',                 'is_both' => 1, 'details' => 'Government-issued voter identity card (front and back required).'],
            ['category' => 'Identity',      'name' => 'Military ID Card',              'is_both' => 1, 'details' => 'Active-duty or veteran military identification card.'],
            ['category' => 'Identity',      'name' => 'Student ID Card',               'is_both' => 1, 'details' => 'Valid student identity card from an accredited institution.'],
            ['category' => 'Identity',      'name' => 'Resident ID Card',              'is_both' => 1, 'details' => 'Government-issued resident identity card.'],
            ['category' => 'Identity',      'name' => 'Permanent Resident Card',       'is_both' => 1, 'details' => 'Permanent residency card (green card or equivalent).'],
            ['category' => 'Identity',      'name' => 'Senior / Old Age Card',         'is_both' => 1, 'details' => 'Government-issued senior citizen identity card.'],
            ['category' => 'Identity',      'name' => 'Social Security Card',          'is_both' => 0, 'details' => 'Social security or national insurance number card.'],
            ['category' => 'Identity',      'name' => 'Refugee Identity Document',     'is_both' => 0, 'details' => 'UNHCR or government-issued refugee identity document.'],

            // ── Proof of Address ────────────────────────────────────────
            ['category' => 'Proof of Address', 'name' => 'Electricity Bill',           'is_both' => 0, 'details' => 'Recent electricity utility bill showing name and address (not older than 3 months).'],
            ['category' => 'Proof of Address', 'name' => 'Gas / Water Bill',           'is_both' => 0, 'details' => 'Recent gas or water utility bill (not older than 3 months).'],
            ['category' => 'Proof of Address', 'name' => 'Internet or Phone Bill',     'is_both' => 0, 'details' => 'Recent internet or telephone bill showing current address.'],
            ['category' => 'Proof of Address', 'name' => 'Bank Statement',             'is_both' => 0, 'details' => 'Bank statement showing your name and current address (within 3 months).'],
            ['category' => 'Proof of Address', 'name' => 'Government / Council Letter','is_both' => 0, 'details' => 'Official letter from a government body showing your address.'],
            ['category' => 'Proof of Address', 'name' => 'Insurance Certificate',      'is_both' => 0, 'details' => 'Insurance certificate or schedule showing current residential address.'],
            ['category' => 'Proof of Address', 'name' => 'Tax Assessment Notice',      'is_both' => 0, 'details' => 'Tax assessment or rates notice issued to your current address.'],
            ['category' => 'Proof of Address', 'name' => 'Lease Agreement (Current)',  'is_both' => 0, 'details' => 'Current signed lease or tenancy agreement showing address.'],
            ['category' => 'Proof of Address', 'name' => 'Payslip with Address',       'is_both' => 0, 'details' => 'Recent payslip that includes your residential address.'],
            ['category' => 'Proof of Address', 'name' => 'Court Document with Address','is_both' => 0, 'details' => 'Official court document showing your current residential address.'],
            ['category' => 'Proof of Address', 'name' => 'Property Tax Bill',          'is_both' => 0, 'details' => 'Municipal property tax or rates bill for your current address.'],

            // ── Financial Documents ─────────────────────────────────────
            ['category' => 'Financial',     'name' => 'Bank Statement (3 months)',     'is_both' => 0, 'details' => 'Bank account statements covering the last 3 months.'],
            ['category' => 'Financial',     'name' => 'Payslip / Salary Slip',         'is_both' => 0, 'details' => 'Most recent payslip or salary statement from employer.'],
            ['category' => 'Financial',     'name' => 'Tax Return',                    'is_both' => 0, 'details' => 'Most recent annual income tax return or filing.'],
            ['category' => 'Financial',     'name' => 'Notice of Assessment',          'is_both' => 0, 'details' => 'Tax authority notice of assessment confirming income.'],
            ['category' => 'Financial',     'name' => 'Business Registration Certificate', 'is_both' => 0, 'details' => 'Official certificate of business registration or incorporation.'],
            ['category' => 'Financial',     'name' => 'ABN / TFN / EIN Certificate',   'is_both' => 0, 'details' => 'Tax identification or business number certificate.'],
            ['category' => 'Financial',     'name' => 'Pension / Benefits Statement',  'is_both' => 0, 'details' => 'Government pension, social security, or benefits statement.'],
            ['category' => 'Financial',     'name' => 'Investment Portfolio Statement', 'is_both' => 0, 'details' => 'Statement from investment, shares, or managed fund account.'],
            ['category' => 'Financial',     'name' => 'Superannuation / Retirement Statement', 'is_both' => 0, 'details' => 'Superannuation fund or retirement account statement.'],
            ['category' => 'Financial',     'name' => 'Income Certificate',            'is_both' => 0, 'details' => 'Official income certificate from employer or government authority.'],
            ['category' => 'Financial',     'name' => 'Rental Income Statement',       'is_both' => 0, 'details' => 'Statement of rental income from property investments.'],

            // ── Employment & Income ─────────────────────────────────────
            ['category' => 'Employment',    'name' => 'Employment Contract',           'is_both' => 0, 'details' => 'Current signed employment contract or letter of appointment.'],
            ['category' => 'Employment',    'name' => 'Letter of Employment',          'is_both' => 0, 'details' => 'Official letter from employer confirming employment and income.'],
            ['category' => 'Employment',    'name' => 'Work Permit',                   'is_both' => 0, 'details' => 'Government-issued permit authorising employment in this country.'],
            ['category' => 'Employment',    'name' => 'Professional License',          'is_both' => 0, 'details' => 'Professional or trade license issued by a regulatory body.'],
            ['category' => 'Employment',    'name' => 'Business License',              'is_both' => 0, 'details' => 'Local government or municipal business operating license.'],
            ['category' => 'Employment',    'name' => 'Employer Reference Letter',     'is_both' => 0, 'details' => 'Character or employment reference letter from a current employer.'],
            ['category' => 'Employment',    'name' => 'Profit & Loss Statement',       'is_both' => 0, 'details' => 'Business profit and loss statement (for self-employed applicants).'],
            ['category' => 'Employment',    'name' => 'Job Offer Letter',              'is_both' => 0, 'details' => 'Signed offer letter from a prospective employer.'],
            ['category' => 'Employment',    'name' => 'Contractor Agreement',          'is_both' => 0, 'details' => 'Freelance or contractor service agreement.'],
            ['category' => 'Employment',    'name' => 'Trade Certificate',             'is_both' => 0, 'details' => 'Recognised trade or vocational qualification certificate.'],

            // ── Rental History ──────────────────────────────────────────
            ['category' => 'Rental History', 'name' => 'Previous Lease Agreement',    'is_both' => 0, 'details' => 'Signed lease agreement from previous tenancy.'],
            ['category' => 'Rental History', 'name' => 'Landlord Reference Letter',   'is_both' => 0, 'details' => 'Reference letter from a previous landlord or property manager.'],
            ['category' => 'Rental History', 'name' => 'Rental Ledger',               'is_both' => 0, 'details' => 'Rental payment ledger showing on-time payment history.'],
            ['category' => 'Rental History', 'name' => 'Bond / Deposit Receipt',      'is_both' => 0, 'details' => 'Receipt for rental bond or security deposit.'],
            ['category' => 'Rental History', 'name' => 'Tenancy History Declaration', 'is_both' => 0, 'details' => 'Signed declaration of previous tenancy and rental history.'],

            // ── Education ───────────────────────────────────────────────
            ['category' => 'Education',     'name' => 'Degree / Diploma Certificate', 'is_both' => 0, 'details' => 'Official degree, diploma, or certificate from an accredited institution.'],
            ['category' => 'Education',     'name' => 'Academic Transcript',          'is_both' => 0, 'details' => 'Official academic transcript or results record.'],
            ['category' => 'Education',     'name' => 'Student Enrollment Letter',    'is_both' => 0, 'details' => 'Official enrollment confirmation letter from the institution.'],
            ['category' => 'Education',     'name' => 'Scholarship Letter',           'is_both' => 0, 'details' => 'Scholarship or bursary award letter.'],
            ['category' => 'Education',     'name' => 'Professional Qualification',   'is_both' => 0, 'details' => 'Recognised professional qualification or certification.'],

            // ── Visa & Immigration ──────────────────────────────────────
            ['category' => 'Visa & Immigration', 'name' => 'Visa Grant Notice',       'is_both' => 0, 'details' => 'Official visa grant or approval notice.'],
            ['category' => 'Visa & Immigration', 'name' => 'Passport Entry Stamp',    'is_both' => 0, 'details' => 'Passport page showing valid entry stamp.'],
            ['category' => 'Visa & Immigration', 'name' => 'Temporary Residence Permit', 'is_both' => 1, 'details' => 'Temporary resident permit card (front and back).'],
            ['category' => 'Visa & Immigration', 'name' => 'Work Visa',               'is_both' => 0, 'details' => 'Valid work visa authorising employment.'],
            ['category' => 'Visa & Immigration', 'name' => 'Residency Application',   'is_both' => 0, 'details' => 'Acknowledgement of residency or citizenship application.'],
            ['category' => 'Visa & Immigration', 'name' => 'Asylum Seeker Document',  'is_both' => 0, 'details' => 'Bridging visa or asylum seeker documentation.'],

            // ── Legal Documents ─────────────────────────────────────────
            ['category' => 'Legal',         'name' => 'Power of Attorney',            'is_both' => 0, 'details' => 'Signed and witnessed power of attorney document.'],
            ['category' => 'Legal',         'name' => 'Court Order',                  'is_both' => 0, 'details' => 'Official court order relevant to tenancy or identity.'],
            ['category' => 'Legal',         'name' => 'Statutory Declaration',        'is_both' => 0, 'details' => 'Signed and witnessed statutory declaration.'],
            ['category' => 'Legal',         'name' => 'Deed Poll (Name Change)',      'is_both' => 0, 'details' => 'Executed deed poll documenting an official name change.'],
            ['category' => 'Legal',         'name' => 'Divorce Certificate',          'is_both' => 0, 'details' => 'Decree absolute or divorce certificate.'],
            ['category' => 'Legal',         'name' => 'Police Check Certificate',     'is_both' => 0, 'details' => 'National police clearance or criminal history check.'],
            ['category' => 'Legal',         'name' => 'Working with Children Check',  'is_both' => 1, 'details' => 'Working with Children Check card (front and back).'],
            ['category' => 'Legal',         'name' => 'Marriage Certificate',         'is_both' => 0, 'details' => 'Official government-issued marriage certificate.'],

            // ── Health & Insurance ──────────────────────────────────────
            ['category' => 'Health & Insurance', 'name' => 'Health Insurance Card',   'is_both' => 1, 'details' => 'Private health insurance membership card (front and back).'],
            ['category' => 'Health & Insurance', 'name' => 'Medicare / Health Card',  'is_both' => 1, 'details' => 'Government health or Medicare card (front and back).'],
            ['category' => 'Health & Insurance', 'name' => 'Contents Insurance Policy', 'is_both' => 0, 'details' => 'Home or renters contents insurance policy certificate.'],
            ['category' => 'Health & Insurance', 'name' => 'Life Insurance Policy',   'is_both' => 0, 'details' => 'Life insurance policy certificate or schedule.'],
            ['category' => 'Health & Insurance', 'name' => 'Medical Certificate',     'is_both' => 0, 'details' => 'Medical certificate relevant to tenancy application.'],
            ['category' => 'Health & Insurance', 'name' => 'Disability Certificate',  'is_both' => 0, 'details' => 'Government-issued disability or carer certificate.'],

            // ── Other ────────────────────────────────────────────────────
            ['category' => 'Other',         'name' => 'Vehicle Registration',         'is_both' => 0, 'details' => 'Current vehicle registration certificate.'],
            ['category' => 'Other',         'name' => 'Pet Registration Certificate', 'is_both' => 0, 'details' => 'Pet registration or council registration certificate.'],
            ['category' => 'Other',         'name' => 'Character Reference Letter',   'is_both' => 0, 'details' => 'Personal character reference letter from a professional contact.'],
            ['category' => 'Other',         'name' => 'Emergency Contact Form',       'is_both' => 0, 'details' => 'Signed emergency contact information form.'],
            ['category' => 'Other',         'name' => 'Next-of-Kin Declaration',      'is_both' => 0, 'details' => 'Signed declaration of next of kin or dependants.'],
            ['category' => 'Other',         'name' => 'Other Personal Document',      'is_both' => 0, 'details' => 'Any other personal document as required by the property manager.'],
        ];

        $rows = array_map(fn($c) => array_merge($c, [
            'owner_user_id' => null,   // global — visible to all owners/tenants
            'tenant_id'     => null,   // available to all tenants
            'status'        => 1,      // ACTIVE
            'created_at'    => $now,
            'updated_at'    => $now,
        ]), $configs);

        foreach (array_chunk($rows, 20) as $chunk) {
            \Illuminate\Support\Facades\DB::table('kyc_configs')->insertOrIgnore($chunk);
        }

        $this->command->info('Seeded ' . count($rows) . ' KYC document configs across 10 categories.');
    }
}
