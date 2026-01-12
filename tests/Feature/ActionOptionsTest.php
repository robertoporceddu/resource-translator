<?php

use Illuminate\Support\Str;
use RobertoPorceddu\ResourceTranslator\Filament\Actions\ChangeTranslationAction;
use RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Models\Post;

describe('ChangeTranslationAction Options', function () {
    beforeEach(function () {
        // Set up available locales config
        config(['translation-manager.available_locales' => [
            ['code' => 'en', 'name' => 'English'],
            ['code' => 'it', 'name' => 'Italian'],
            ['code' => 'es', 'name' => 'Spanish'],
            ['code' => 'fr', 'name' => 'French'],
        ]]);
    });

    describe('action setup', function () {
        it('has correct default name', function () {
            expect(ChangeTranslationAction::getDefaultName())->toBe('changeTranslation');
        });

        it('class exists', function () {
            expect(class_exists(ChangeTranslationAction::class))->toBeTrue();
        });

        it('can get options closure', function () {
            $action = ChangeTranslationAction::make();

            // Access the protected options property
            $optionsClosure = (function () {
                return $this->options;
            })->call($action);

            expect($optionsClosure)->toBeInstanceOf(Closure::class);
        });
    });

    describe('options generation with model', function () {
        it('generates options from available locales', function () {
            $uuid = Str::uuid()->toString();

            // Create only English post
            $englishPost = Post::withoutGlobalScopes()->create([
                'title' => 'English Post',
                'content' => 'English content',
                'locale' => 'en',
                'translation_group_uuid' => $uuid,
            ]);

            $action = ChangeTranslationAction::make();

            // Get the options closure and execute it
            $optionsClosure = (function () {
                return $this->options;
            })->call($action);

            // Execute the closure with the record
            $options = $optionsClosure($englishPost);

            // Should not include current locale (en)
            expect($options)->not->toHaveKey('en');
            // Should include other locales
            expect($options)->toHaveKeys(['it', 'es', 'fr']);
        });

        it('shows (1) for existing translations and (0) for missing', function () {
            $uuid = Str::uuid()->toString();

            // Create posts in en and it
            app()->setLocale('en');
            $englishPost = Post::create([
                'title' => 'English Post',
                'translation_group_uuid' => $uuid,
            ]);

            app()->setLocale('it');
            Post::create([
                'title' => 'Post Italiano',
                'translation_group_uuid' => $uuid,
            ]);

            // Refresh the English post to get fresh translations
            $englishPost = Post::withoutGlobalScopes()->find($englishPost->id);

            $action = ChangeTranslationAction::make();

            $optionsClosure = (function () {
                return $this->options;
            })->call($action);

            $options = $optionsClosure($englishPost);

            // Italian exists, should show (1)
            expect($options['it'] ?? '')->toContain('(1)');
            // Spanish doesn't exist, should show (0)
            expect($options['es'] ?? '')->toContain('(0)');
            // French doesn't exist, should show (0)
            expect($options['fr'] ?? '')->toContain('(0)');
        });

        it('excludes current record locale from options', function () {
            $uuid = Str::uuid()->toString();

            app()->setLocale('it');
            $italianPost = Post::create([
                'title' => 'Post Italiano',
                'translation_group_uuid' => $uuid,
            ]);

            $action = ChangeTranslationAction::make();

            $optionsClosure = (function () {
                return $this->options;
            })->call($action);

            $options = $optionsClosure($italianPost);

            // Should not include current locale (it)
            expect($options)->not->toHaveKey('it');
            // Should include other locales
            expect($options)->toHaveKeys(['en', 'es', 'fr']);
        });

        it('handles empty available locales config', function () {
            config(['translation-manager.available_locales' => []]);

            $post = Post::withoutGlobalScopes()->create([
                'title' => 'Test Post',
                'locale' => 'en',
                'translation_group_uuid' => Str::uuid()->toString(),
            ]);

            $action = ChangeTranslationAction::make();

            $optionsClosure = (function () {
                return $this->options;
            })->call($action);

            $options = $optionsClosure($post);
            expect($options)->toBeEmpty();
        });
    });

    describe('placeholder', function () {
        it('has a placeholder set', function () {
            $action = ChangeTranslationAction::make();

            $placeholder = (function () {
                return $this->placeholder;
            })->call($action);

            expect($placeholder)->not->toBeNull();
        });
    });
});
