<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegisterTenantTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        // Remove any tenant databases created by the tests (sqlite files in database/).
        foreach (glob(database_path('tenant*')) ?: [] as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        parent::tearDown();
    }

    public function test_register_page_loads_on_central_domain(): void
    {
        $this->get('http://localhost/register')
            ->assertOk()
            ->assertSee('Subdomain')
            ->assertSee('.localhost');
    }

    public function test_valid_subdomain_creates_tenant_and_domain(): void
    {
        $this->post('http://localhost/register', ['subdomain' => 'Acme '])
            ->assertRedirect(route('tenants.create'))
            ->assertSessionHas('tenant_url', 'http://acme.localhost')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('domains', ['domain' => 'acme.localhost']);
        $this->assertSame(1, Tenant::count());
    }

    public function test_duplicate_subdomain_is_rejected(): void
    {
        $this->post('http://localhost/register', ['subdomain' => 'acme']);

        $this->post('http://localhost/register', ['subdomain' => 'acme'])
            ->assertSessionHasErrors('subdomain');

        $this->assertSame(1, Tenant::count());
    }

    #[DataProvider('invalidSubdomains')]
    public function test_invalid_subdomain_is_rejected(string $subdomain): void
    {
        $this->post('http://localhost/register', ['subdomain' => $subdomain])
            ->assertSessionHasErrors('subdomain');

        $this->assertSame(0, Tenant::count());
    }

    public static function invalidSubdomains(): array
    {
        return [
            'empty' => [''],
            'too short' => ['ab'],
            'reserved' => ['www'],
            'leading hyphen' => ['-acme'],
            'trailing hyphen' => ['acme-'],
            'dot' => ['a.b.c'],
            'html' => ['<script>'],
            'too long' => [str_repeat('a', 64)],
        ];
    }

    public function test_tenant_routes_are_not_served_on_central_domain(): void
    {
        $this->get('http://localhost/register')->assertOk();
        $this->get('http://unknown.localhost/register')->assertNotFound();
        $this->get('http://unknown.localhost/')->assertNotFound();
    }
}
