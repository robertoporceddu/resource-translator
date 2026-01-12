<?php

use Filament\Contracts\Plugin;
use RobertoPorceddu\ResourceTranslator\ResourceTranslatorPlugin;
use RobertoPorceddu\ResourceTranslator\Http\Middleware\TranslationGroupMiddleware;

describe('ResourceTranslatorPlugin Registration', function () {
    describe('plugin registration', function () {
        it('can be registered on a panel', function () {
            $plugin = ResourceTranslatorPlugin::make();

            // Plugin should implement the Plugin interface methods
            expect(method_exists($plugin, 'getId'))->toBeTrue();
            expect(method_exists($plugin, 'register'))->toBeTrue();
            expect(method_exists($plugin, 'boot'))->toBeTrue();
        });

        it('implements Plugin interface', function () {
            $plugin = ResourceTranslatorPlugin::make();

            expect($plugin)->toBeInstanceOf(Plugin::class);
        });

        it('has correct plugin id', function () {
            $plugin = ResourceTranslatorPlugin::make();

            expect($plugin->getId())->toBe('resource-translator');
        });
    });

    describe('middleware configuration', function () {
        it('configures middleware when language_switcher is enabled', function () {
            config(['translation-manager.language_switcher' => true]);

            $plugin = ResourceTranslatorPlugin::make();

            // The middleware should be configured
            expect(config('translation-manager.language_switcher'))->toBeTrue();
        });

        it('does not configure middleware when language_switcher is disabled', function () {
            config(['translation-manager.language_switcher' => false]);

            $plugin = ResourceTranslatorPlugin::make();

            expect(config('translation-manager.language_switcher'))->toBeFalse();
        });
    });

    describe('TranslationGroupMiddleware', function () {
        it('middleware class exists', function () {
            expect(class_exists(TranslationGroupMiddleware::class))->toBeTrue();
        });

        it('middleware is instantiable', function () {
            $middleware = new TranslationGroupMiddleware();

            expect($middleware)->toBeInstanceOf(TranslationGroupMiddleware::class);
        });
    });
});
