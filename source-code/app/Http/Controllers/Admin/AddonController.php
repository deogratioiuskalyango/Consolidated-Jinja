<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AddonController extends Controller
{
    /**
     * All known addons for this application.
     * key  = the code string used throughout the system (isAddonInstalled, settings table, etc.)
     */
    const ADDONS = [
        'PROTYSAAS' => [
            'name'          => 'Multi-Owner / SaaS',
            'description'   => 'Enable multi-owner mode with SaaS subscription plans, owner onboarding, and package management.',
            'icon'          => 'ri-building-4-line',
            'color'         => 'primary',
            'codecanyon_url'=> 'https://codecanyon.net/item/zaiproty-property-management-laravel-script/43413718',
            'license'       => true,
        ],
        'PROTYSMS' => [
            'name'          => 'SMS Gateway',
            'description'   => 'Send automated SMS alerts to tenants and owners via Twilio, Nexmo, and other providers.',
            'icon'          => 'ri-message-2-line',
            'color'         => 'success',
            'codecanyon_url'=> 'https://codecanyon.net/item/zaiproty-property-management-laravel-script/43413718',
            'license'       => true,
        ],
        'PROTYAGREEMENT' => [
            'name'          => 'Agreement & e-Sign',
            'description'   => 'Digital lease agreements with e-signature support for rental and tenancy contracts.',
            'icon'          => 'ri-file-text-line',
            'color'         => 'warning',
            'codecanyon_url'=> 'https://codecanyon.net/item/zaiproty-property-management-laravel-script/43413718',
            'license'       => true,
        ],
        'PROTYTENANCY' => [
            'name'          => 'Advanced Tenancy',
            'description'   => 'Extended tenancy management with custom fields, workflows, and reporting.',
            'icon'          => 'ri-home-gear-line',
            'color'         => 'info',
            'codecanyon_url'=> 'https://codecanyon.net/item/zaiproty-property-management-laravel-script/43413718',
            'license'       => true,
        ],
        'PROTYLISTING' => [
            'name'          => 'Property Listing',
            'description'   => 'Public-facing property listing portal with search, filters, and enquiry management.',
            'icon'          => 'ri-list-check-2',
            'color'         => 'danger',
            'codecanyon_url'=> 'https://codecanyon.net/item/zaiproty-property-management-laravel-script/43413718',
            'license'       => true,
        ],
    ];

    public function index()
    {
        $addons = collect(self::ADDONS)->map(function ($addon, $code) {
            $installedBuild  = isAddonInstalled($code);          // 0 = not installed
            $currentVersion  = getOption($code . '_current_version', null);
            $hasUploadedZip  = file_exists(storage_path('app/addons/' . $code . '.zip'));

            return array_merge($addon, [
                'code'            => $code,
                'is_installed'    => $installedBuild > 0,
                'installed_build' => $installedBuild,
                'current_version' => $currentVersion,
                'uploaded_zip'    => $hasUploadedZip ? $code . '.zip' : null,
            ]);
        });

        return view('admin.addons.index', [
            'title'  => __('Addons'),
            'addons' => $addons,
        ]);
    }
}
