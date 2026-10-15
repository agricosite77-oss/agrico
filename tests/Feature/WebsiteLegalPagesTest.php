<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteLegalPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the Privacy Policy page loads successfully with all key sections.
     */
    public function test_privacy_policy_page_loads_successfully(): void
    {
        $response = $this->get(route('website.privacy'));

        $response->assertStatus(200);
        $response->assertSee('Privacy Policy');
        $response->assertSee('How we collect, use, and protect your information');
        $response->assertSee('agricosite77@gmail.com');
        $response->assertSee('Cagayan State University – Lal-lo Campus');
        $response->assertSee('Data Privacy Act of 2012');

        // Check key anchor IDs
        $response->assertSee('id="introduction"', false);
        $response->assertSee('id="information-we-collect"', false);
        $response->assertSee('id="how-we-use-information"', false);
        $response->assertSee('id="public-map-visibility"', false);
        $response->assertSee('id="sharing-and-third-parties"', false);
        $response->assertSee('id="data-storage-and-security"', false);
        $response->assertSee('id="your-rights"', false);
        $response->assertSee('id="children-and-minors"', false);
        $response->assertSee('id="cookies-and-storage"', false);
        $response->assertSee('id="changes-to-policy"', false);
        $response->assertSee('id="contact-us"', false);

        // Check TOC elements & Cross-Link to Terms
        $response->assertSee('id="legalTocNav"', false);
        $response->assertSee('id="legalBackToTop"', false);
        $response->assertSee(route('website.terms'));
    }

    /**
     * Test that the Terms of Service page loads successfully with all key sections.
     */
    public function test_terms_of_service_page_loads_successfully(): void
    {
        $response = $this->get(route('website.terms'));

        $response->assertStatus(200);
        $response->assertSee('Terms of Service');
        $response->assertSee('CRITICAL AGRICULTURAL');
        $response->assertSee('SCIENTIFIC DISCLAIMER');
        $response->assertSee('Lal-lo, Cagayan');
        $response->assertSee('15 years of age or older');

        // Check key anchor IDs
        $response->assertSee('id="acceptance-of-terms"', false);
        $response->assertSee('id="about-agrico-disclaimer"', false);
        $response->assertSee('id="account-registration"', false);
        $response->assertSee('id="account-security"', false);
        $response->assertSee('id="acceptable-use"', false);
        $response->assertSee('id="user-content-license"', false);
        $response->assertSee('id="moderation-suspension"', false);
        $response->assertSee('id="mobile-app-updates"', false);
        $response->assertSee('id="disclaimer-liability"', false);
        $response->assertSee('id="service-modifications"', false);
        $response->assertSee('id="termination"', false);
        $response->assertSee('id="governing-law"', false);
        $response->assertSee('id="revisions-to-terms"', false);
        $response->assertSee('id="contact-us"', false);

        // Check TOC elements & Cross-Link to Privacy
        $response->assertSee('id="legalTocNav"', false);
        $response->assertSee('id="legalBackToTop"', false);
        $response->assertSee(route('website.privacy'));
    }

    /**
     * Test that legal links are present on the Registration page.
     */
    public function test_registration_page_contains_terms_and_privacy_links(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertSee(route('website.terms'));
        $response->assertSee(route('website.privacy'));
        $response->assertSee('target="_blank"', false);
    }

    /**
     * Test that legal links are present on the Login page.
     */
    public function test_login_page_contains_terms_and_privacy_links(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee(route('website.terms'));
        $response->assertSee(route('website.privacy'));
    }

    /**
     * Test that the site footer contains functional links to Privacy and Terms.
     */
    public function test_site_footer_contains_terms_and_privacy_links(): void
    {
        $response = $this->get(route('website.home'));

        $response->assertStatus(200);
        $response->assertSee(route('website.privacy'));
        $response->assertSee(route('website.terms'));
    }
}
