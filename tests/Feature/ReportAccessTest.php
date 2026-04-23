<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_access_report_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
            'email' => 'superadmin-test@example.com',
        ]);

        $response = $this->actingAs($user)->get(route('super-admin.reports.index'));

        $response->assertOk();
    }

    public function test_customer_cannot_access_super_admin_reports(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer-test@example.com',
        ]);

        $response = $this->actingAs($user)->get(route('super-admin.reports.index'));

        $response->assertForbidden();
    }

    public function test_admin_rental_cannot_access_super_admin_commission_report(): void
    {
        $user = User::factory()->create([
            'role' => 'admin_rental',
            'email' => 'admin-test@example.com',
        ]);

        $response = $this->actingAs($user)->get(route('super-admin.reports.commissions'));

        $response->assertForbidden();
    }
}