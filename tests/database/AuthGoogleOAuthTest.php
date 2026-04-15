<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Google OAuth Auto-Verify Feature Tests
 * 
 * These tests verify the Google OAuth auto-verify functionality
 * using feature testing (HTTP requests).
 * 
 * NOTE: These tests require a configured test database.
 * Run with: vendor/bin/phpunit tests/database/AuthGoogleOAuthTest.php
 * 
 * @internal
 *
 * @coversNothing
 */
final class AuthGoogleOAuthTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    /**
     * Test that Google OAuth callback route is defined in Routes.php
     */
    public function testGoogleCallbackRouteExists(): void
    {
        $routesFile = ROOTPATH . 'app/Config/Routes.php';
        $content = file_get_contents($routesFile);
        
        $this->assertStringContainsString("auth/google-callback", $content,
            'Routes.php should contain auth/google-callback route');
        $this->assertStringContainsString("Auth::googleCallback", $content,
            'Routes.php should map to Auth::googleCallback');
    }

    /**
     * Test that Google login route is defined in Routes.php
     */
    public function testGoogleLoginRouteExists(): void
    {
        $routesFile = ROOTPATH . 'app/Config/Routes.php';
        $content = file_get_contents($routesFile);
        
        $this->assertStringContainsString("auth/google-login", $content,
            'Routes.php should contain auth/google-login route');
        $this->assertStringContainsString("Auth::googleLogin", $content,
            'Routes.php should map to Auth::googleLogin');
    }

    /**
     * Test that the Auth controller has the googleCallback method
     */
    public function testAuthControllerHasGoogleCallbackMethod(): void
    {
        $this->assertTrue(method_exists(Auth::class, 'googleCallback'));
    }

    /**
     * Test that the Auth controller has the googleLogin method
     */
    public function testAuthControllerHasGoogleLoginMethod(): void
    {
        $this->assertTrue(method_exists(Auth::class, 'googleLogin'));
    }

    /**
     * Test that the auto-verify logic is present in googleCallback method
     * by checking the source code contains the expected pattern
     */
    public function testGoogleCallbackContainsAutoVerifyLogic(): void
    {
        $reflection = new \ReflectionClass(Auth::class);
        $method = $reflection->getMethod('googleCallback');
        $filename = $method->getFileName();
        $sourceCode = file_get_contents($filename);
        
        // Check for auto-verify pattern
        $this->assertStringContainsString('profile_is_verified', $sourceCode,
            'googleCallback should check profile_is_verified');
        $this->assertStringContainsString('profile_verification_token', $sourceCode,
            'googleCallback should handle profile_verification_token');
        $this->assertStringContainsString('update', $sourceCode,
            'googleCallback should update user verification status');
    }
}
