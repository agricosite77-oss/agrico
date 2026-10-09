<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminWebsiteBridgeTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;
    public function test_unauthenticated_admin_go_to_website_redirects_to_home_not_login(): void
    {
        $response = $this->get(route('admin.go-to-website'));

        $response->assertStatus(302);
        $response->assertRedirect(route('website.home'));
    }

    public function test_authenticated_admin_go_to_website_syncs_session_and_redirects_to_home(): void
    {
        $admin = Admin::where('email', 'agricosite77@gmail.com')->first();
        if (!$admin) {
            $admin = Admin::create([
                'admin_id'  => 'ADM-999',
                'full_name' => 'Test Admin',
                'email'     => 'testadmin_bridge@agrico.com',
                'password'  => bcrypt('password'),
            ]);
        }

        $response = $this->actingAs($admin, 'admin')->get(route('admin.go-to-website'));

        $response->assertStatus(302);
        $response->assertRedirect(route('website.home'));
        $this->assertTrue(Auth::guard('web')->check());

        // Now follow redirect to website home
        $homeResponse = $this->get(route('website.home'));
        $homeResponse->assertStatus(200);
    }

    public function test_manage_request_review_modal_displays_map_for_soil_coordinates_request(): void
    {
        $admin = Admin::create([
            'admin_id'  => 'ADM-MAP-TEST',
            'full_name' => 'Admin Reviewer',
            'email'     => 'admin_reviewer@agrico.com',
            'password'  => bcrypt('password'),
        ]);

        $req = \App\Models\UserRequest::create([
            'request_code' => 'REQ-PIN-TEST-99',
            'user_name'    => 'Farmer Test',
            'user_email'   => 'farmer@agrico.test',
            'type'         => 'Soil Coordinates',
            'description'  => 'Soil Sample Location Pin #99 - Barangay: Jurisdiction [Covered Area: 500 m²]. Coords: (18.235583661021, 121.67995641251). Notes: Soil sample classified as Light Yellowish Brown (2.5Y 6/2).',
            'status'       => 'Pending',
            'submitted_at' => now(),
        ]);

        \Livewire\Livewire::actingAs($admin, 'admin')
            ->test(\App\Livewire\Admin\Request\ManageRequest::class)
            ->call('viewRequest', $req->user_request_id)
            ->assertSee('Review User Request')
            ->assertSee('Soil Sample Location Map')
            ->assertSee('review-request-map-' . $req->user_request_id)
            ->assertSee('18.2356')
            ->assertSee('121.6800')
            ->assertSee('Jurisdiction');
    }
}
