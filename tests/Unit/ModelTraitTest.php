<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Models\Post;
use RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Models\Article;
use RobertoPorceddu\ResourceTranslator\Traits\Models\HasTranslations;

describe('HasTranslations Model Trait', function () {
    describe('LOCALE_COLUMN constant', function () {
        it('has the correct LOCALE_COLUMN constant', function () {
            expect(Post::LOCALE_COLUMN)->toBe('locale');
        });

        it('constant is accessible from class', function () {
            expect(defined(Post::class . '::LOCALE_COLUMN'))->toBeTrue();
        });
    });

    describe('trait usage', function () {
        it('trait is used by Post model', function () {
            $traits = class_uses_recursive(Post::class);

            expect($traits)->toContain(HasTranslations::class);
        });

        it('trait is used by Article model', function () {
            $traits = class_uses_recursive(Article::class);

            expect($traits)->toContain(HasTranslations::class);
        });
    });

    describe('translations method', function () {
        it('exists on the model', function () {
            expect(method_exists(Post::class, 'translations'))->toBeTrue();
        });

        it('returns a Builder instance', function () {
            $post = createPost();
            $translations = $post->translations();

            expect($translations)->toBeInstanceOf(Builder::class);
        });
    });

    describe('translations attribute', function () {
        it('getTranslationsAttribute exists', function () {
            expect(method_exists(Post::class, 'getTranslationsAttribute'))->toBeTrue();
        });

        it('returns a Collection', function () {
            $post = createPost();

            expect($post->translations)->toBeInstanceOf(Collection::class);
        });
    });

    describe('save method', function () {
        it('sets the locale automatically', function () {
            app()->setLocale('en');
            $post = createPost();

            expect($post->locale)->toBe('en');
        });

        it('sets locale to current app locale', function () {
            app()->setLocale('it');
            $post = createPost();

            expect($post->locale)->toBe('it');
        });

        it('generates a translation_group_uuid for new records', function () {
            $post = createPost();

            expect($post->translation_group_uuid)
                ->not->toBeNull()
                ->toBeUuid();
        });

        it('preserves translation_group_uuid on update', function () {
            $post = createPost();
            $originalUuid = $post->translation_group_uuid;

            $post->title = 'Updated Title';
            $post->save();

            expect($post->translation_group_uuid)->toBe($originalUuid);
        });

        it('uses provided translation_group_uuid if set before save', function () {
            $customUuid = \Illuminate\Support\Str::uuid()->toString();
            
            $post = new Post([
                'title' => 'Test',
                'content' => 'Content',
                'translation_group_uuid' => $customUuid,
            ]);
            $post->save();

            expect($post->translation_group_uuid)->toBe($customUuid);
        });
    });

    describe('booted method', function () {
        it('booted method exists', function () {
            expect(method_exists(Post::class, 'booted'))->toBeTrue();
        });
    });
});
