<?php

use Illuminate\Support\Str;
use RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Models\Post;
use RobertoPorceddu\ResourceTranslator\Tests\Fixtures\Models\Article;

describe('Model Translations Feature', function () {
    describe('creating translations', function () {
        it('creates a post with locale and uuid', function () {
            app()->setLocale('en');
            $post = createPost(['title' => 'English Post']);

            expect($post->locale)->toBe('en');
            expect($post->translation_group_uuid)->toBeUuid();
        });

        it('creates translations in different locales with same uuid', function () {
            $uuid = Str::uuid()->toString();

            app()->setLocale('en');
            $englishPost = Post::create([
                'title' => 'English Post',
                'translation_group_uuid' => $uuid,
            ]);

            app()->setLocale('it');
            $italianPost = Post::create([
                'title' => 'Post Italiano',
                'translation_group_uuid' => $uuid,
            ]);

            app()->setLocale('es');
            $spanishPost = Post::create([
                'title' => 'Post en Español',
                'translation_group_uuid' => $uuid,
            ]);

            expect($englishPost->translation_group_uuid)->toBe($uuid);
            expect($italianPost->translation_group_uuid)->toBe($uuid);
            expect($spanishPost->translation_group_uuid)->toBe($uuid);
            expect($englishPost->locale)->toBe('en');
            expect($italianPost->locale)->toBe('it');
            expect($spanishPost->locale)->toBe('es');
        });

        it('different posts get different uuids', function () {
            $post1 = createPost(['title' => 'Post 1']);
            $post2 = createPost(['title' => 'Post 2']);

            expect($post1->translation_group_uuid)->not->toBe($post2->translation_group_uuid);
        });
    });

    describe('retrieving translations', function () {
        beforeEach(function () {
            $this->uuid = Str::uuid()->toString();

            app()->setLocale('en');
            $this->englishPost = Post::create([
                'title' => 'English Post',
                'content' => 'English content',
                'translation_group_uuid' => $this->uuid,
            ]);

            app()->setLocale('it');
            $this->italianPost = Post::create([
                'title' => 'Post Italiano',
                'content' => 'Contenuto italiano',
                'translation_group_uuid' => $this->uuid,
            ]);

            app()->setLocale('es');
            $this->spanishPost = Post::create([
                'title' => 'Post en Español',
                'content' => 'Contenido en español',
                'translation_group_uuid' => $this->uuid,
            ]);
        });

        it('translations method returns all translations', function () {
            $translations = $this->englishPost->translations()->get();

            expect($translations)->toHaveCount(3);
        });

        it('translations attribute returns collection', function () {
            $translations = $this->englishPost->translations;

            expect($translations)->toHaveCount(3);
            expect($translations->pluck('locale')->toArray())->toContain('en', 'it', 'es');
        });

        it('can find specific translation by locale', function () {
            $italian = $this->englishPost->translations()->where('locale', 'it')->first();

            expect($italian)->not->toBeNull();
            expect($italian->title)->toBe('Post Italiano');
        });

        it('translations from different post link to same group', function () {
            $translationsFromEnglish = $this->englishPost->translations;
            $translationsFromItalian = $this->italianPost->translations;

            expect($translationsFromEnglish->pluck('id')->sort()->values()->toArray())
                ->toBe($translationsFromItalian->pluck('id')->sort()->values()->toArray());
        });
    });

    describe('updating translations', function () {
        it('preserves translation_group_uuid on update', function () {
            $post = createPost(['title' => 'Original']);
            $originalUuid = $post->translation_group_uuid;

            $post->title = 'Updated Title';
            $post->content = 'Updated content';
            $post->save();

            $post->refresh();

            expect($post->translation_group_uuid)->toBe($originalUuid);
            expect($post->title)->toBe('Updated Title');
        });

        it('preserves uuid even when explicitly setting different value on update', function () {
            $post = createPost(['title' => 'Original']);
            $originalUuid = $post->translation_group_uuid;

            $post->translation_group_uuid = 'different-uuid';
            $post->save();

            expect($post->translation_group_uuid)->toBe($originalUuid);
        });
    });

    describe('soft deletes integration', function () {
        it('soft deleted articles are not included in translations', function () {
            $uuid = Str::uuid()->toString();

            app()->setLocale('en');
            $englishArticle = Article::create([
                'title' => 'English Article',
                'body' => 'English body',
                'translation_group_uuid' => $uuid,
            ]);

            app()->setLocale('it');
            $italianArticle = Article::create([
                'title' => 'Articolo Italiano',
                'body' => 'Corpo italiano',
                'translation_group_uuid' => $uuid,
            ]);

            // Delete the Italian article
            $italianArticle->delete();

            $translations = $englishArticle->translations()->get();

            expect($translations)->toHaveCount(1);
            expect($translations->first()->locale)->toBe('en');
        });

        it('withTrashed still includes soft deleted articles', function () {
            $uuid = Str::uuid()->toString();

            app()->setLocale('en');
            Article::create([
                'title' => 'English Article',
                'body' => 'English body',
                'translation_group_uuid' => $uuid,
            ]);

            app()->setLocale('it');
            $italianArticle = Article::create([
                'title' => 'Articolo Italiano',
                'body' => 'Corpo italiano',
                'translation_group_uuid' => $uuid,
            ]);

            $italianArticle->delete();

            $allArticles = Article::withoutGlobalScopes()
                ->withTrashed()
                ->where('translation_group_uuid', $uuid)
                ->get();

            expect($allArticles)->toHaveCount(2);
        });
    });

    describe('locale scoping', function () {
        it('without global scope both posts are found', function () {
            $uuid = Str::uuid()->toString();

            app()->setLocale('en');
            Post::create([
                'title' => 'English Post',
                'translation_group_uuid' => $uuid,
            ]);

            app()->setLocale('it');
            Post::create([
                'title' => 'Italian Post',
                'translation_group_uuid' => $uuid,
            ]);

            // Without global scope, both should be found
            $allPosts = Post::withoutGlobalScopes()
                ->where('translation_group_uuid', $uuid)
                ->get();

            expect($allPosts)->toHaveCount(2);
        });
    });
});
