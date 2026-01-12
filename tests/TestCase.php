<?php

namespace RobertoPorceddu\ResourceTranslator\Tests;

use Filament\FilamentServiceProvider;
use Filament\Support\SupportServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use RobertoPorceddu\ResourceTranslator\ResourceTranslatorServiceProvider;
use RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Models\User;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            SupportServiceProvider::class,
            FilamentServiceProvider::class,
            ResourceTranslatorServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Configure the database for testing
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Set app key
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));

        // Configure auth
        $app['config']->set('auth.providers.users.model', User::class);

        // Configure translation-manager
        $app['config']->set('translation-manager.available_locales', [
            ['code' => 'en', 'name' => 'English'],
            ['code' => 'it', 'name' => 'Italian'],
            ['code' => 'es', 'name' => 'Spanish'],
            ['code' => 'fr', 'name' => 'French'],
        ]);

        $app['config']->set('translation-manager.language_switcher', true);

        // Set default locale
        $app['config']->set('app.locale', 'en');
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    /**
     * Create a test user and authenticate.
     */
    protected function actingAsUser(): User
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user);

        return $user;
    }
}
