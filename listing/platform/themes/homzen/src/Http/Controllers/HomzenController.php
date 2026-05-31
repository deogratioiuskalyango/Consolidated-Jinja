<?php

namespace Theme\Homzen\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Location\Models\City;
use Botble\Location\Repositories\Interfaces\CityInterface;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Http\Controllers\PublicController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Theme\Homzen\Actions\GetProjectsAction;
use Theme\Homzen\Actions\GetPropertiesAction;
use Theme\Homzen\Http\Resources\ProjectResource;
use Theme\Homzen\Http\Resources\PropertyResource;

class HomzenController extends PublicController
{
    public function getUnifiedLogin(Request $request)
    {
        if (Auth::guard('account')->check() && Auth::guard('customer')->check()) {
            return redirect()->intended(route('public.account.dashboard'));
        }

        if ($request->filled('redirect')) {
            session(['url.intended' => $request->query('redirect')]);
        } elseif (! session()->has('url.intended') && url()->previous() !== url()->current()) {
            session(['url.intended' => url()->previous()]);
        }

        SeoHelper::setTitle(__('Login'));
        Theme::set('breadcrumbEnabled', 'no');

        return Theme::scope('unified-login')->render();
    }

    public function postUnifiedLogin(Request $request): BaseHttpResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
            'remember' => ['nullable'],
        ]);

        $login = $validated['email'];
        $password = $validated['password'];
        $remember = $request->boolean('remember');

        $account = DB::table('re_accounts')
            ->where('email', $login)
            ->orWhere('username', $login)
            ->first();

        $customer = DB::table('ht_customers')
            ->where('email', $login)
            ->first();

        $verifiedHash = null;
        $verifiedFrom = null;

        if ($account && Hash::check($password, $account->password)) {
            if ($account->blocked_at) {
                throw ValidationException::withMessages([
                    'email' => [__('Your property account has been blocked. Please contact support.')],
                ]);
            }

            $verifiedHash = $account->password;
            $verifiedFrom = 'account';
        }

        if (! $verifiedHash && $customer && Hash::check($password, $customer->password)) {
            $verifiedHash = $customer->password;
            $verifiedFrom = 'customer';
        }

        if (! $verifiedHash) {
            throw ValidationException::withMessages([
                'email' => [__('These credentials do not match our records.')],
            ]);
        }

        if (! $customer) {
            $names = $this->splitLoginName($account?->first_name, $account?->last_name, $login);

            $customerId = DB::table('ht_customers')->insertGetId([
                'first_name' => $names['first_name'],
                'last_name' => $names['last_name'],
                'email' => $account?->email ?: $login,
                'password' => $verifiedHash,
                'phone' => $account?->phone,
                'confirmed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $customer = DB::table('ht_customers')->where('id', $customerId)->first();
        } elseif ($verifiedFrom === 'account' && ! Hash::check($password, $customer->password)) {
            DB::table('ht_customers')->where('id', $customer->id)->update([
                'password' => $verifiedHash,
                'updated_at' => now(),
            ]);
        }

        if (! $account) {
            $names = $this->splitLoginName($customer?->first_name, $customer?->last_name, $login);

            $accountId = DB::table('re_accounts')->insertGetId([
                'first_name' => $names['first_name'],
                'last_name' => $names['last_name'],
                'email' => $customer?->email ?: $login,
                'username' => $this->uniqueAccountUsername($login),
                'password' => $verifiedHash,
                'phone' => $customer?->phone,
                'confirmed_at' => now(),
                'is_public_profile' => 1,
                'approved_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $account = DB::table('re_accounts')->where('id', $accountId)->first();
        } elseif ($verifiedFrom === 'customer' && ! Hash::check($password, $account->password)) {
            DB::table('re_accounts')->where('id', $account->id)->update([
                'password' => $verifiedHash,
                'updated_at' => now(),
            ]);
        }

        Auth::guard('account')->loginUsingId($account->id, $remember);
        Auth::guard('customer')->loginUsingId($customer->id, $remember);

        $request->session()->regenerate();

        return $this
            ->httpResponse()
            ->setNextUrl(session()->pull('url.intended') ?: route('public.account.dashboard'))
            ->setMessage(__('You are now signed in across Jinja Consolidated Properties.'));
    }

    public function redirectToUnifiedLogin()
    {
        return redirect()->route('customer.login');
    }

    private function splitLoginName(?string $firstName, ?string $lastName, string $fallback): array
    {
        if ($firstName || $lastName) {
            return [
                'first_name' => $firstName ?: 'Jinja',
                'last_name' => $lastName ?: 'Member',
            ];
        }

        $name = str($fallback)->before('@')->replace(['.', '_', '-'], ' ')->title()->explode(' ')->filter()->values();

        return [
            'first_name' => $name->first() ?: 'Jinja',
            'last_name' => $name->slice(1)->implode(' ') ?: 'Member',
        ];
    }

    private function uniqueAccountUsername(string $login): string
    {
        $base = str($login)->before('@')->slug()->limit(40, '')->value() ?: 'jcp-member';
        $username = $base;
        $counter = 1;

        while (DB::table('re_accounts')->where('username', $username)->exists()) {
            $username = $base . '-' . $counter++;
        }

        return $username;
    }

    public function getHardwareQuote()
    {
        SeoHelper::setTitle(__('Request a hardware quotation'))
            ->setDescription(__('Send your bill of quantities or hardware material list and receive supplier quotations.'));

        Theme::breadcrumb()->add(__('Hardware quotation'));

        return Theme::scope('hardware-quote')->render();
    }

    public function postHardwareQuote(Request $request): BaseHttpResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'project_type' => ['nullable', 'string', 'max:120'],
            'delivery_location' => ['nullable', 'string', 'max:180'],
            'needed_by' => ['nullable', 'date'],
            'materials' => ['required', 'string', 'max:5000'],
            'budget' => ['nullable', 'string', 'max:120'],
        ]);

        DB::table('hardware_quote_requests')->insert([
            ...$validated,
            'status' => 'new',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $this
            ->httpResponse()
            ->setNextUrl(route('public.hardware-quote'))
            ->setMessage(__('Your quotation request has been received. Our team will match it with suitable hardware suppliers.'));
    }

    public function getAdminHardwareQuotations()
    {
        page_title()->setTitle(__('Hardware quotations'));

        $quotes = DB::table('hardware_quote_requests')->latest()->paginate(25);

        return view(Theme::getThemeNamespace('views.admin.hardware-quotations'), compact('quotes'));
    }

    public function ajaxGetProperties(Request $request, GetPropertiesAction $getPropertiesAction): BaseHttpResponse
    {
        $request->validate([
            'type' => ['nullable', Rule::in(PropertyTypeEnum::values())],
            'limit' => ['required', 'integer'],
            'is_featured' => ['boolean'],
            'category_id' => ['nullable', 'string'],
            'category_ids' => ['nullable', 'array'],
        ]);

        $properties = $getPropertiesAction->handle(
            $request->integer('limit', 6),
            $request->input('category_id'),
            $request->string('type'),
            $request->boolean('is_featured'),
            (array) $request->input('category_ids', [])
        );

        return $this
            ->httpResponse()
            ->setData(view(
                Theme::getThemeNamespace('views.real-estate.properties.index'),
                ['properties' => $properties, 'itemsPerRow' => 3]
            )->render());
    }

    public function ajaxGetPropertiesForMap(Request $request): JsonResource
    {
        $validated = $request->validate([
            'k' => ['nullable', 'string'],
            'type' => ['nullable', Rule::in(PropertyTypeEnum::values())],
            'bedroom' => ['nullable'],
            'bathroom' => ['nullable'],
            'floor' => ['nullable'],
            'min_price' => ['nullable', 'numeric'],
            'max_price' => ['nullable', 'numeric'],
            'min_square' => ['nullable', 'numeric'],
            'max_square' => ['nullable', 'numeric'],
            'project' => ['nullable', 'string'],
            'category_id' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'city_id' => ['nullable', 'integer'],
            'state' => ['nullable', 'string'],
            'state_id' => ['nullable', 'integer'],
            'location' => ['nullable', 'string'],
        ]);

        $validated['keyword'] = $validated['k'] ?? null;

        $params = [
            'with' => RealEstateHelper::getPropertyRelationsQuery(),
            'paginate' => [
                'per_page' => 20,
                'current_paged' => $request->integer('page', 1),
            ],
        ];

        $properties = app(PropertyInterface::class)->getProperties($validated, $params);

        return $this
            ->httpResponse()
            ->setData(PropertyResource::collection($properties))
            ->toApiResponse();
    }

    public function ajaxGetProjects(Request $request, GetProjectsAction $getProjectsAction): BaseHttpResponse
    {
        if (! RealEstateHelper::isEnabledProjects()) {
            return $this->httpResponse()->setData([]);
        }

        $request->validate([
            'limit' => ['required', 'integer'],
            'is_featured' => ['boolean'],
            'category_id' => ['nullable', 'string'],
            'category_ids' => ['nullable', 'array'],
        ]);

        $projects = $getProjectsAction->handle(
            $request->integer('limit', 6),
            $request->input('category_id'),
            $request->boolean('is_featured'),
            (array) $request->input('category_ids', [])
        );

        return $this
            ->httpResponse()
            ->setData(view(
                Theme::getThemeNamespace('views.real-estate.projects.grid'),
                compact('projects')
            )->render());
    }

    public function ajaxSearchProjects(Request $request): BaseHttpResponse
    {
        $request->validate([
            'k' => ['nullable', 'string'],
        ]);

        $projects = Project::query()
            ->when($request->filled('k'), function (Builder $query) use ($request): void {
                $query->where('name', 'LIKE', '%' . $request->input('k') . '%');
            })
            ->select(['id', 'name'])
            ->take(10)
            ->oldest('name')
            ->get();

        return $this
            ->httpResponse()
            ->setData(view(
                Theme::getThemeNamespace('views.real-estate.partials.filters.projects-suggestion'),
                compact('projects')
            )->render());
    }

    public function ajaxGetProjectsForMap(Request $request, ProjectInterface $projectRepository)
    {
        $filters = $request->input();

        $filters['keyword'] = $filters['k'] ?? null;

        $params = [
            'with' => RealEstateHelper::getProjectRelationsQuery(),
            'paginate' => [
                'per_page' => 20,
                'current_paged' => $request->integer('page', 1),
            ],
        ];

        $projects = $projectRepository->getProjects($filters, $params);

        return $this
            ->httpResponse()
            ->setData(ProjectResource::collection($projects))
            ->toApiResponse();
    }

    public function ajaxGetCities(Request $request)
    {
        if (! is_plugin_active('location')) {
            return $this->httpResponse()->setData([]);
        }

        $request->validate([
            'location' => ['nullable', 'string'],
            'page' => ['nullable', 'integer'],
            'minimal' => ['nullable', 'boolean'],
        ]);

        // Handle minimal mode for niceSelect dropdown
        if ($request->boolean('minimal')) {
            $page = $request->integer('page', 1);
            $perPage = 10;

            // Use direct query to the City model for proper pagination
            $query = City::query()
                ->wherePublished()
                ->with('state')
                ->orderBy('name');

            // Add search filter if provided
            if ($request->input('location')) {
                $query->where('name', 'LIKE', '%' . $request->input('location') . '%');
            }

            // Get paginated results directly from database
            $cities = $query->paginate($perPage, ['*'], 'page', $page);

            // Format data for niceSelect
            $items = $cities->map(function ($city) {
                return [
                    'id' => $city->id,
                    'text' => $city->name . ($city->state ? ', ' . $city->state->name : ''),
                ];
            });

            return $this->httpResponse()
                ->setData([
                    'items' => $items,
                    'has_more' => $cities->hasMorePages(),
                    'total' => $cities->total(),
                ])
                ->toApiResponse();
        }

        // For non-minimal requests, use the original implementation
        $cities = app(CityInterface::class)->filters($request->input('location'));

        return $this
            ->httpResponse()
            ->setData(
                view(
                    Theme::getThemeNamespace('views.real-estate.partials.filters.cities-suggestion'),
                    compact('cities')
                )->render()
            );
    }

    public function getWishlist(Request $request, PropertyInterface $propertyRepository, ProjectInterface $projectRepository)
    {
        abort_unless(RealEstateHelper::isEnabledWishlist(), 404);

        SeoHelper::setTitle(__('Wishlist'))
            ->setDescription(__('Wishlist'));

        $propertyWishlist = isset($_COOKIE['wishlist']) ? explode(',', $_COOKIE['wishlist']) : [];
        $propertyWishlist = array_filter($propertyWishlist);
        $projectWishlist = isset($_COOKIE['project_wishlist']) ? explode(',', $_COOKIE['project_wishlist']) : [];
        $projectWishlist = array_filter($projectWishlist);

        $properties = collect();
        $projects = collect();

        if (! empty($propertyWishlist)) {
            $properties = $propertyRepository->advancedGet([
                'condition' => [
                    ['re_properties.id', 'IN', $propertyWishlist],
                ],
                'order_by' => [
                    're_properties.id' => 'DESC',
                ],
                'paginate' => [
                    'per_page' => (int) theme_option('number_of_properties_per_page', 12),
                    'current_paged' => $request->integer('page', 1),
                ],
                'with' => RealEstateHelper::getPropertyRelationsQuery(),
            ]);
        }

        if (! empty($projectWishlist)) {
            $projects = $projectRepository->advancedGet([
                'condition' => [
                    ['re_projects.id', 'IN', $projectWishlist],
                ],
                'order_by' => [
                    're_projects.id' => 'DESC',
                ],
                'paginate' => [
                    'per_page' => (int) theme_option('number_of_properties_per_page', 12),
                    'current_paged' => $request->integer('page', 1),
                ],
                'with' => RealEstateHelper::getProjectRelationsQuery(),
            ]);
        }

        Theme::breadcrumb()->add(__('Wishlist'));

        return Theme::scope('real-estate.wishlist', compact('properties', 'projects'))->render();
    }
}
