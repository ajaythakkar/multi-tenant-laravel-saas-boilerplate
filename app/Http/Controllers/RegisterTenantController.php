<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisterTenantController extends Controller
{
    /**
     * Subdomains that can never be registered by a tenant.
     */
    private const RESERVED_SUBDOMAINS = ['www', 'admin', 'api', 'app', 'mail', 'ftp', 'localhost'];

    public function create(): View
    {
        return view('register', [
            'centralDomain' => $this->centralDomain(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['subdomain' => strtolower(trim((string) $request->input('subdomain')))]);

        $centralDomain = $this->centralDomain();

        $validated = $request->validate([
            'subdomain' => [
                'required',
                'string',
                'min:3',
                'max:63',
                // A single DNS label: letters, digits and inner hyphens.
                'regex:/^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/',
                Rule::notIn(self::RESERVED_SUBDOMAINS),
            ],
        ]);

        $domain = $validated['subdomain'].'.'.$centralDomain;

        if (DB::table('domains')->where('domain', $domain)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['subdomain' => 'This subdomain is already taken.']);
        }

        $tenant = Tenant::create(['plan' => 'free']);
        $tenant->domains()->create(['domain' => $domain]);

        return redirect()
            ->route('tenants.create')
            ->with('tenant_url', $request->getScheme().'://'.$domain.$this->portSuffix($request));
    }

    /**
     * The domain that tenant subdomains are created under.
     */
    private function centralDomain(): string
    {
        return config('tenancy.central_domains')[0];
    }

    private function portSuffix(Request $request): string
    {
        $port = $request->getPort();

        return in_array($port, [80, 443], true) ? '' : ':'.$port;
    }
}
