<?php

namespace Tests\Feature;

use App\Models\FAQ;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteFaqTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test that the FAQ page loads successfully with 200 OK.
     */
    public function test_faq_page_loads_successfully(): void
    {
        $response = $this->get(route('website.faq'));

        $response->assertStatus(200);
        $response->assertSee('Frequently Asked Questions');
        $response->assertSee('Everything you need to know about AgriCo');
        $response->assertSee('agricosite77@gmail.com');
    }

    /**
     * Test that key categories and question anchors are present in the response.
     */
    public function test_faq_page_renders_key_questions_and_anchors(): void
    {
        $response = $this->get(route('website.faq'));

        $response->assertStatus(200);

        // Check key anchors
        $response->assertSee('id="what-is-agrico"', false);
        $response->assertSee('id="how-to-register"', false);
        $response->assertSee('id="account-approval"', false);
        $response->assertSee('id="forgot-password"', false);
        $response->assertSee('id="no-code"', false);
        $response->assertSee('id="why-suspended"', false);
        $response->assertSee('id="how-to-scan"', false);
        $response->assertSee('id="photo-guide"', false);
        $response->assertSee('id="what-is-munsell"', false);
        $response->assertSee('id="map-pins"', false);
        $response->assertSee('id="download-app"', false);
        $response->assertSee('id="terms-and-privacy"', false);
        $response->assertSee('id="contact-support"', false);

        // Check search box & expand toggle
        $response->assertSee('id="faqSearchInput"', false);
        $response->assertSee('id="faqExpandAllToggle"', false);
    }

    /**
     * Test that other website pages contain functional contextual links to the FAQ.
     */
    public function test_contextual_faq_links_exist_on_auth_and_feature_pages(): void
    {
        // 1. Login page
        $loginRes = $this->get(route('login'));
        $loginRes->assertStatus(200);
        $loginRes->assertSee(route('website.faq'));

        // 2. Register page
        $regRes = $this->get(route('register'));
        $regRes->assertStatus(200);
        $regRes->assertSee(route('website.faq') . '#terms-and-privacy');
        $regRes->assertSee(route('website.faq') . '#account-approval');

        // 3. Forgot Password page
        $forgotRes = $this->get(route('password.request'));
        $forgotRes->assertStatus(200);
        $forgotRes->assertSee(route('website.faq') . '#forgot-password');

        // 4. Map page
        $mapRes = $this->get(route('website.map'));
        $mapRes->assertStatus(200);
        $mapRes->assertSee(route('website.faq') . '#map-pins');
    }

    /**
     * Test fallback when database has custom FAQ records.
     */
    public function test_faq_page_loads_database_records_when_present(): void
    {
        FAQ::create([
            'question' => 'Custom Database Test Question?',
            'category' => 'Testing',
            'answer'   => 'This is an answer from the database.',
            'status'   => 'Active',
        ]);

        $response = $this->get(route('website.faq'));

        $response->assertStatus(200);
        $response->assertSee('Custom Database Test Question?');
        $response->assertSee('This is an answer from the database.');

        // Clean up database test record
        FAQ::where('question', 'Custom Database Test Question?')->delete();
    }
}
