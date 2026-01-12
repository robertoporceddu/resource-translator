<?php

describe('Translation Manager Configuration', function () {
    describe('available_locales configuration', function () {
        it('has default locales configured', function () {
            $locales = config('translation-manager.available_locales');

            expect($locales)->not->toBeNull();
            expect($locales)->toBeArray();
        });

        it('each locale has code and name', function () {
            $locales = config('translation-manager.available_locales');

            foreach ($locales as $locale) {
                expect($locale)->toHaveKeys(['code', 'name']);
            }
        });

        it('locale codes are strings', function () {
            $locales = config('translation-manager.available_locales');

            foreach ($locales as $locale) {
                expect($locale['code'])->toBeString();
            }
        });

        it('locale names are strings', function () {
            $locales = config('translation-manager.available_locales');

            foreach ($locales as $locale) {
                expect($locale['name'])->toBeString();
            }
        });
    });

    describe('language_switcher configuration', function () {
        it('is configured', function () {
            $switcher = config('translation-manager.language_switcher');

            expect($switcher)->not->toBeNull();
        });

        it('is boolean', function () {
            $switcher = config('translation-manager.language_switcher');

            expect($switcher)->toBeBool();
        });

        it('defaults to true in test environment', function () {
            $switcher = config('translation-manager.language_switcher');

            expect($switcher)->toBeTrue();
        });
    });

    describe('configuration can be modified', function () {
        it('can add new locale at runtime', function () {
            $locales = config('translation-manager.available_locales');
            $locales[] = ['code' => 'de', 'name' => 'German'];
            config(['translation-manager.available_locales' => $locales]);

            $updatedLocales = config('translation-manager.available_locales');
            $codes = collect($updatedLocales)->pluck('code')->toArray();

            expect($codes)->toContain('de');
        });

        it('can remove locale at runtime', function () {
            $locales = config('translation-manager.available_locales');
            $locales = array_filter($locales, fn($l) => $l['code'] !== 'es');
            config(['translation-manager.available_locales' => array_values($locales)]);

            $updatedLocales = config('translation-manager.available_locales');
            $codes = collect($updatedLocales)->pluck('code')->toArray();

            expect($codes)->not->toContain('es');
        });

        it('can disable language switcher at runtime', function () {
            config(['translation-manager.language_switcher' => false]);

            expect(config('translation-manager.language_switcher'))->toBeFalse();
        });
    });
});

